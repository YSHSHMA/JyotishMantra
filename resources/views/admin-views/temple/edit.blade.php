@extends('layouts.back-end.app')

@section('title', translate('temple_edit'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('temple_edit') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.temple.update', $temple['id']) }}" method="post"
            enctype="multipart/form-data" id="product_form">
            @csrf

            <div class="card">
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $language)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab  {{ $language == $defaultLanguage ? 'active' : '' }}"
                                    href="#"
                                    id="{{ $language }}-link">{{ getLanguageName($language) . '(' . strtoupper($language) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    @foreach ($languages as $language)
                        <?php
                        if (count($temple['translations'])) {
                            $translate = [];
                            foreach ($temple['translations'] as $translation) {
                                if ($translation->locale == $language && $translation->key == 'name') {
                                    $translate[$language]['name'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'short_description') {
                                    $translate[$language]['short_description'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'facilities') {
                                    $translate[$language]['facilities'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'tips_restrictions') {
                                    $translate[$language]['tips_restrictions'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'details') {
                                    $translate[$language]['details'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'more_details') {
                                    $translate[$language]['more_details'] = $translation->value;
                                }
                            }
                        }
                        ?>
                        <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $language }}-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="title-color"
                                        for="{{ $language }}_name">{{ translate('temple_name') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="name[]"
                                        id="{{ $language }}_name"
                                        value="{{ $translate[$language]['name'] ?? $temple['name'] }}" class="form-control"
                                        placeholder="{{ translate('new_temple') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_short_description">{{ translate('short_description') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="short_description[]"
                                        id="{{ $language }}_short_description"
                                        value="{{ $translate[$language]['short_description'] ?? $temple['short_description'] }}"
                                        class="form-control" placeholder="{{ translate('short_description') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $language }}_facilities">{{ translate('facilities') }}
                                            ({{ strtoupper($language) }})</label>
                                        <input type="text" {{ $language == 'en' ? 'required' : '' }}
                                            name="facilities[]" id="{{ $language }}_facilities"
                                            value="{{ $translate[$language]['facilities'] ?? $temple['facilities'] }}"
                                            class="form-control" placeholder="{{ translate('facilities') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $language }}_tips_restrictions">{{ translate('tips_restrictions') }}
                                            ({{ strtoupper($language) }})</label>
                                        <input type="text" {{ $language == 'en' ? 'required' : '' }} name="tips_restrictions[]"
                                            id="{{ $language }}_tips_restrictions"
                                            value="{{ $translate[$language]['tips_restrictions'] ?? $temple['tips_restrictions'] }}"
                                            class="form-control" placeholder="{{ translate('tips_restrictions') }}" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $language }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group pt-4">
                                        <label class="title-color">{{ translate('temple_description') }}
                                            ({{ strtoupper($language) }})</label>
                                        <textarea name="details[]" class="ckeditor" id="editor{{ $language }}">{!! $translate[$language]['details'] ?? $temple['details'] !!}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pt-4">
                                        <label class="title-color">{{ translate('more_details') }}
                                            ({{ strtoupper($language) }})</label>
                                        <textarea name="more_details[]" class="ckeditor" id="editor{{ $language }}">{!! $translate[$language]['more_details'] ?? $temple['more_details'] !!}</textarea>
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
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('state_select') }}</label>
                                <select class="js-example-basic-multiple js-states js-example-responsive form-control action-get-request-onchange"
                                name="state_id" id="state_id"
                                data-url-prefix="{{ url('/admin/temple/get-cities?id=') }}"
                                data-element-id="sub-cities-select"
                                data-element-type="select">
                                <option value="0" selected disabled>---{{ translate('select') }}---</option>
                                    @foreach ($stateList as $stateItem)
                                        <option value="{{ $stateItem['id'] }}" {{ $stateItem->id==$temple['id'] ? 'selected' : ''}}>{{ $stateItem['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('cities_select') }}</label>
                                <select  name="city_id" class="js-example-basic-multiple js-cities js-example-responsive form-control"
                                    data-id="{{ $temple['city_id'] }}" id="sub-cities-select">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('opening_time') }}</label>
                                <input type="text" name="opening_time" id="opentime" class="form-control"
                                    placeholder="{{ translate('opening_time') }}" value="{{ isset($temple['opening_time']) ? date('H:i', strtotime($temple['opening_time'])) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('closeing_time') }}</label>
                                <input type="text" name="closeing_time" id="closetime" class="form-control"
                                    placeholder="{{ translate('closeing_time') }}" value="{{ isset($temple['closeing_time']) ? date('H:i', strtotime($temple['closeing_time'])) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('require_time') }}</label>
                                <input type="text" name="require_time" class="form-control"
                                    placeholder="{{ translate('require_time') }}" value="{{$temple['entry_fee']}}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('entry_fee') }}</label>
                                <input type="text" name="entry_fee" class="form-control"
                                    placeholder="{{ translate('entry_fee') }}" value="{{ $temple['entry_fee'] }}">
                            </div>
                        </div>



                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">
                                    {{ translate('search_tags') }}
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                        title="{{ translate('add_the_product_search_tag_for_this_product_that_customers_can_use_to_search_quickly') }}">
                                        <img width="16"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                            alt="">
                                    </span>
                                </label>
                                <input type="text" class="form-control" name="tags"
                                    value="@foreach ($temple->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                    data-role="tagsinput">
                            </div>
                        </div> --}}
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <i class="tio-user-big"></i>
                                <h4 class="mb-0">{{ translate('temple_video') }}</h4>
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                    title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}.">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                        alt="">
                                </span>
                            </div>
                            <div class="mb-3">
                                <label class="title-color mb-0">{{ translate('youtube_video_link') }}</label>
                                <span class="text-info">
                                    ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
                            </div>
                            <input type="text" name="video_url"
                                placeholder="{{ translate('ex') . ': https://www.youtube.com/embed/5R06LRdUCSE' }}"
                                class="form-control">
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
                                            <label for="name"
                                                class="title-color text-capitalize font-weight-bold mb-0">{{ translate('temple_thumbnail') }}</label>
                                            <span
                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                    alt="">
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="custom_upload_input">
                                            <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id=""
                                            data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                        @if (File::exists(base_path('storage/app/public/temple/thumbnail/' . $temple->thumbnail)))
                                            <span
                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d-flex">
                                                <i class="tio-delete"></i>
                                            </span>
                                        @else
                                            <span
                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>
                                        @endif

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer"
                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none" alt=""
                                                src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . $temple->thumbnail, type: 'backend-product') }}">
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
                    <div class="col-md-9">
                        <div class="card mt-1 rest-part">
                            <div class="card-header">
                                <div class="d-flex gap-2">
                                    <i class="tio-user-big"></i>
                                    <h4 class="mb-0">
                                        {{ translate('seo_section') }}
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top"
                                            title="{{ translate('add_meta_titles_descriptions_and_images_for_products') . ', ' . translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
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
                                                    title="{{ translate('add_the_products_title_name_taglines_etc_here') . ' ' . translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                        alt="">
                                                </span>
                                            </label>
                                            <input type="text" name="meta_title" value="{{ $temple['meta_title'] }}"
                                                placeholder="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="title-color">
                                                {{ translate('meta_Description') }}
                                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="{{ translate('write_a_short_description_of_the_InHouse_shops_product') . ' ' . translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                        alt="">
                                                </span>
                                            </label>
                                            <textarea rows="4" type="text" name="meta_description" class="form-control">{{ $temple['meta_description'] }}</textarea>
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
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
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
            
                                                        @if (File::exists(base_path('storage/app/public/temple/meta/' . $temple['meta_image'])))
                                                            <span
                                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d-flex">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                        @else
                                                            <span
                                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                        @endif
            
                                                        <div class="img_area_with_preview position-absolute z-index-2 d-flex">
                                                            <img id="pre_meta_image_viewer"
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                alt=""
                                                                src="{{ getValidImage(path: 'storage/app/public/temple/meta/' . $temple['meta_image'], type: 'backend-banner') }}">
                                                        </div>
                                                        <div
                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                            <div class="d-flex flex-column justify-content-center align-items-center">
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
                        </div>
                    </div>


                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
            </div>

        </form>
    </div>

    <span id="image-path-of-product-upload-icon"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-want-to-add-or-update-this-service"
        data-text="{{ translate('want_to_update_this_service') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-service-added-successfully" data-text="{{ translate('service_added_successfully') }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script type="text/javascript">
        $('.service-add-requirements-check').on('click', function() {
            getServiceAddRequirementsCheck()
        });
        </script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script>
          // time picker
          $('#timepicker').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
        initSample();
    </script>
    <script>
        "use strict";


        let thumbnail =
            '{{ productImagePath('thumbnail') . '/' . $temple->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}';
        $(function() {
            if (imageCount > 0) {
                $("#coba").spartanMultiImagePicker({
                    fieldName: 'images[]',
                    maxCount: colors === 0 ? 15 : imageCount,
                    rowHeight: 'auto',
                    groupClassName: 'col-6 col-md-4 col-xl-3 col-xxl-2',
                    maxFileSize: '',
                    placeholderImage: {
                        image: '{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}',
                        width: '100%',
                    },
                    dropFileLabel: "Drop Here",
                    onAddRow: function(index, file) {},
                    onRenderedPreview: function(index) {},
                    onRemoveRow: function(index) {},
                    onExtensionErr: function() {
                        toastr.error(messagePleaseOnlyInputPNGOrJPG, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    },
                    onSizeErr: function() {
                        toastr.error(messageFileSizeTooBig, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                });
            }

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ productImagePath('thumbnail') . '/' . $temple->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function() {
                    toastr.error(messagePleaseOnlyInputPNGOrJPG, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function() {
                    toastr.error(messageFileSizeTooBig, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

        });




        $(document).ready(function() {
            setTimeout(function() {
                let states = $("#state_id").val();
                let cities = $("#sub-cities-select").attr("data-id");
                getRequestFunctionality('{{ route('admin.temple.get-cities') }}?id=' + states + '&cities=' + cities, 'sub-cities-select', 'select');
            }, 100)
        });
    </script>
@endpush
