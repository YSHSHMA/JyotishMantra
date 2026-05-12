@extends('layouts.back-end.app')

@section('title', translate('add_temple'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
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

        <form class="product-form text-start" action="{{ route('admin.temple.add-new') }}" method="POST"
            enctype="multipart/form-data" id="services_form">
            @csrf
            <div class="card">
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $lang)
                            <li class="nav-item">
                                <span
                                    class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                    id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">

                    @foreach ($languages as $lang)
                        <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $lang }}-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="title-color"for="{{ $lang }}_cities">{{ translate('temple_name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }}
                                            name="name[]" id="{{ $lang }}_name" class="form-control"
                                            placeholder="{{ translate('temple_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="title-color"for="{{ $lang }}_short_description">{{ translate('short_description') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }}
                                            name="short_description[]" id="{{ $lang }}_short_description"
                                            class="form-control" placeholder="short descscription">

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="title-color"for="{{ $lang }}_facilities">{{ translate('facilities') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }}
                                            name="facilities[]" id="{{ $lang }}_facilities" class="form-control"
                                            placeholder="{{ translate('facilities') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="title-color"for="{{ $lang }}_tips_restrictions">{{ translate('tips_&_restrictions') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }}
                                            name="tips_restrictions[]" id="{{ $lang }}_tips_restrictionsr"
                                            class="form-control" placeholder="{{ translate('tips__&_restrictions') }}">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="title-color"
                                        for="{{ $lang }}_details">{{ translate('temple_description') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" name="details[]" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="title-color"
                                        for="{{ $lang }}_more_details">{{ translate('more_details') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" name="more_details[]" required></textarea>

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
                                <select class="js-select2-custom form-control action-get-request-onchange" name="state_id" id="state_id"
                                data-url-prefix="{{ url('/admin/temple/get-cities?id=') }}"
                                data-element-id="sub-cities-select"
                                data-element-type="select" required>
                                <option value="{{ old('id') }}" selected
                                disabled>{{ translate('select_State') }}</option>
                                    @foreach ($stateList as $stateItem)
                                        <option value="{{ $stateItem['id'] }}">{{ $stateItem['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('cities_select') }}</label>
                                <select class="js-select2-custom form-control action-get-request-onchange" name="city_id"
                                        id="sub-cities-select">
                                    <option value="{{ null }}" selected disabled>
                                        {{ translate('select_Cities') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('opening_time') }}</label>
                                <input type="text" name="opening_time" id="opentime" class="form-control"
                                    placeholder="{{ translate('opening_time') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('closeing_time') }}</label>
                                <input type="text" name="closeing_time" id="closetime" class="form-control"
                                    placeholder="{{ translate('closeing_time') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('require_time') }}</label>
                                <input type="text" name="require_time" class="form-control"
                                    placeholder="{{ translate('require_time') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('entry_fee') }}</label>
                                <input type="text" name="entry_fee" class="form-control"
                                    placeholder="{{ translate('entry_fee') }}">
                            </div>
                        </div>



                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">
                                    {{ translate('search_tags') }}
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                        title="{{ translate('add_the_temple_search_tag_for_this_temple_that_customers_can_use_to_search_quickly') }}">
                                        <img width="16"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                            alt="">
                                    </span>
                                </label>
                                <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}"
                                    name="tags" data-role="tagsinput">
                            </div>
                        </div> --}}
                        <div class="col-md-6">
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
                                            <input type="file" name="image"
                                                class="custom-upload-input-file action-upload-color-image" id=""
                                                data-imgpreview="pre_img_viewer"
                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                            <span
                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none"
                                                    src="dummy" alt="">
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
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                            data-placement="top"
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
                                            <input type="text" name="meta_title"
                                                placeholder="{{ translate('meta_Title') }}" class="form-control">
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
                                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}">
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
    });    --}}
    
@endpush
