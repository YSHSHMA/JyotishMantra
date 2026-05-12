@extends('layouts.back-end.app')

@section('title', translate('Edit|Chadhava'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        #weekDays,
        #startendDate {
            transition: all 0.3s ease;
        }
    </style>
@endpush


@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('Edit|Chadhava') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.chadhava.update', $chadhava['id']) }}" method="post"
            enctype="multipart/form-data" id="chadhava_form">
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
                        if (count($chadhava['translations'])) {
                            $translate = [];
                            foreach ($chadhava['translations'] as $translation) {
                                if ($translation->locale == $language && $translation->key == 'name') {
                                    $translate[$language]['name'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'description') {
                                    $translate[$language]['description'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'short_details') {
                                    $translate[$language]['short_details'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'chadhava_venue') {
                                    $translate[$language]['chadhava_venue'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'pooja_heading') {
                                    $translate[$language]['pooja_heading'] = $translation->value;
                                }
                            }
                        }
                        ?>
                        <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $language }}-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="bhagwan_id" class="title-color">
                                        {{ translate('Bhagwan') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="bhagwan_id" class="form-control bhagwan_select"
                                        onchange="$('.bhagwan_select').val(this.value)" id="bhagwan_id">
                                        <option value="">{{ translate('Select Bhagwan') }}</option>
                                        @forelse($bhagwanId as $bhagwan)
                                            <option value="{{ $bhagwan->id }}"
                                                {{ $bhagwan->id == $chadhava->bhagwan_id ? 'selected' : '' }}>
                                                {{ $bhagwan->name }}
                                            </option>
                                        @empty
                                            <option value="">{{ translate('No Categories Available') }}</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="title-color"
                                        for="{{ $language }}_name">{{ translate('chadhava_name') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="name[]"
                                        id="{{ $language }}_name"
                                        value="{{ $translate[$language]['name'] ?? $chadhava['name'] }}"
                                        class="form-control" placeholder="{{ translate('chadhava_name') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_pooja_heading">{{ translate('chadhava_heading') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="pooja_heading[]" id="{{ $language }}_pooja_heading"
                                        value="{{ $translate[$language]['pooja_heading'] ?? $chadhava['pooja_heading'] }}"
                                        class="form-control" placeholder="{{ translate('chadhava_heading_special') }}"
                                        required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_short_details">{{ translate('short_details') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="short_details[]"
                                        id="{{ $language }}_short_details"
                                        value="{{ $translate[$language]['short_details'] ?? $chadhava['short_details'] }}"
                                        class="form-control" placeholder="{{ translate('short_details') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_chadhava_venue">{{ translate('chadhava_venue') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="chadhava_venue[]"
                                        id="{{ $language }}_chadhava_venue"
                                        value="{{ $translate[$language]['chadhava_venue'] ?? $chadhava['chadhava_venue'] }}"
                                        class="form-control" placeholder="{{ translate('chadhava_venue') }}" required>
                                </div>
                            </div>

                            <input type="hidden" name="lang[]" value="{{ $language }}">
                            <div class="form-group pt-4">
                                <label class="title-color"
                                    for="{{ $language }}_description">{{ translate('description') }}
                                    ({{ strtoupper($language) }})</label>
                                <textarea class="ckeditor" id="editor{{ $language }}" name="description[]">{!! $translate[$language]['description'] ?? $chadhava['details'] !!}</textarea>
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
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group" id="chadhavaType">
                                <label class="title-color">{{ translate('chadhava_type') }}</label>
                                <select name="chadhava_type" id="chadhava_type" class="chadhava form-control">
                                    <option value="0" {{ $chadhava['chadhava_type'] == 0 ? 'selected' : '' }}>Weekly
                                    </option>
                                    <option value="1" {{ $chadhava['chadhava_type'] == 1 ? 'selected' : '' }}>Special
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-6" id="weekDays">
                            @php
                                $weekDays = isset($chadhava['chadhava_week'])
                                    ? json_decode($chadhava['chadhava_week'], true)
                                    : [];
                            @endphp
                            <label class="title-color">{{ translate('Chadhava_week') }}</label>
                            <select name="chadhava_week[]" id="weekdays" class="js-select2-custom form-control"
                                multiple>

                                {{-- Default Option: Only selected if $weekDays is empty --}}
                                {{-- <option value="0" disabled {{ empty($weekDays) ? 'selected' : '' }}>
                                    ---{{ translate('select') }}---
                                </option> --}}
                                <option value="sunday" {{ in_array('sunday', $weekDays) ? 'selected' : '' }}>Sunday
                                </option>
                                <option value="monday" {{ in_array('monday', $weekDays) ? 'selected' : '' }}>Monday
                                </option>
                                <option value="tuesday" {{ in_array('tuesday', $weekDays) ? 'selected' : '' }}>Tuesday
                                </option>
                                <option value="wednesday" {{ in_array('wednesday', $weekDays) ? 'selected' : '' }}>
                                    Wednesday</option>
                                <option value="thursday" {{ in_array('thursday', $weekDays) ? 'selected' : '' }}> Thursday
                                </option>
                                <option value="friday" {{ in_array('friday', $weekDays) ? 'selected' : '' }}>Friday
                                </option>
                                <option value="saturday" {{ in_array('saturday', $weekDays) ? 'selected' : '' }}> Saturday
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-lg-6" id="startendDate" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('start_date') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="start_date"
                                            id="StartDateSelected" placeholder="Start Date"
                                            value="{{ $chadhava['start_date'] }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('end_date') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="end_date"
                                            id="EndDateSelected" placeholder="End Date"
                                            value="{{ $chadhava['end_date'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">{{ translate('Is_Vedio') }}
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                        title="{{ translate('Please check the checkbox if is_vedio  is selected. Checked: Anushthan, Not Checked: Not Vedio') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                            alt="">
                                    </span>
                                </label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_video"
                                        name="is_video_checkbox" value="1"
                                        {{ !empty($chadhava['chadhava_week']) ? 'checked' : '' }}>

                                    <label class="custom-control-label text-muted" for="is_video">
                                        {{ translate('is_video') }}
                                    </label>

                                    <input type="hidden" id="is_video_hidden" name="is_video" value="0">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
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
                                    value="@foreach ($chadhava->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                    data-role="tagsinput">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label
                                    class="title-color d-flex align-items-center gap-2">{{ translate('charity_product') }}
                                </label>
                                <select class="js-select2-custom form-control" name="product_id[]"
                                    data-element-id="sub-category-select" data-element-type="select" required multiple>
                                    <option value="" disabled>Select Product</option>
                                    @php
                                        $ChadhavaProduct = array_unique(json_decode($chadhava['product_id'], true));
                                    @endphp
                                    @foreach ($productes as $product)
                                        @if ($product->category_id == 33 && in_array($product->id, $ChadhavaProduct))
                                            <option value="{{ $product->id }}" selected>{{ $product->name }}</option>
                                        @else
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Package Price Div --}}
            <div class="mt-3 rest-part">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name"
                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Chadhava_thumbnail') }}</label>
                                        <span
                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                            title="{{ translate('add_your_products_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
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

                                        @if (File::exists(base_path('storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail)))
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
                                                src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail, type: 'backend-product') }}">
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

                                    <p class="text-muted mt-2">{{ translate('image_format') }} :
                                        {{ 'Jpg, png, jpeg, webp ' }}<br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}</p>
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
                                <p class="text-muted">{{ translate('upload_additional_chadhava_images') }}</p>
                                <div class="coba-area">

                                    <div class="row g-2" id="additional_Image_Section">

                                        @if (is_array($chadhava['images']) && count($chadhava['images']) == 0)
                                            @foreach (json_decode($chadhava['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.chadhava.delete-image', ['id' => $chadhava['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo, type: 'backend-product') }}">
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
                                        @else
                                            @foreach (json_decode($chadhava['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.chadhava.delete-image', ['id' => $chadhava['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/chadhava/' . $photo, type: 'backend-product') }}">
                                                        </div>
                                                        <div
                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                            <div
                                                                class="d-flex flex-column justify-content-center align-items-center">
                                                                <img alt="" class="w-75"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
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
                </div>

                <input type="hidden" id="images" value="{{ $chadhava->images }}">
                <input type="hidden" id="chadhava_id" value="{{ $chadhava->id }}">
                <input type="hidden" id="remove_url"
                    value="{{ route('admin.chadhava.delete-image', ['id' => $chadhava['id'], 'name' => $photo]) }}">
            </div>



            <div class="card mt-3 rest-part">
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
                                <input type="text" name="meta_title" value="{{ $chadhava['meta_title'] }}"
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
                                <textarea rows="4" type="text" name="meta_description" class="form-control">{{ $chadhava['meta_description'] }}</textarea>
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

                                            @if (File::exists(base_path('storage/app/public/pooja/meta/' . $chadhava['meta_image'])))
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
                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/meta/' . $chadhava['meta_image'], type: 'backend-banner') }}">
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
    <span id="message-want-to-add-or-update-this-chadhava"
        data-text="{{ translate('want_to_update_this_chadhava') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-chadhava-added-successfully" data-text="{{ translate('chadhava_added_successfully') }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

    <script type="text/javascript">
        $('.chadhava-add-requirements-check').on('click', function() {
            getServiceAddRequirementsCheck()
        });
    </script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script>
        $('#pooja_time').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
        $('#event-date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd/mm/yyyy',
            modal: true,
            footer: true
        });
        initSample();
    </script>
    <script>
        "use strict";


        let imageCount = {{ 15 - count(json_decode($chadhava->images)) }};
        let thumbnail =
            '{{ productImagePath('thumbnail') . '/' . $chadhava->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}';
        let remove_url = $('#remove_url').val();
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
                    image: '{{ productImagePath('thumbnail') . '/' . $chadhava->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}',
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
    </script>
    <script>
        // datepicker
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        //Start Date
        $('#StartDateSelected').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy/mm/dd',
            modal: true,
            footer: true,
            minDate: today,
            todayHighlight: true
        });
        // end Date
        $('#EndDateSelected').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy/mm/dd',
            modal: true,
            footer: true,
            minDate: today,
            todayHighlight: true
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
            height: 320, // Set the desired height
                width: '80%'
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
            function toggleSections() {
                var selectedValue = $('#chadhava_type').val();
                if (selectedValue == '1') {
                    $('#weekDays').hide();
                    $('#startendDate').show();
                } else {
                    $('#weekDays').show();
                    $('#startendDate').hide();
                }
            }

            // Run on page load
            toggleSections();

            // Run on change
            $('#chadhava_type').on('change', function() {
                toggleSections();
            });
            // Weekdatas
            $('#weekdays').on('change', function() {
                $(this).find('option[value="0"]').prop('selected', false);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#is_video').on('change', function() {
                $('#is_video_hidden').val($(this).is(':checked') ? 1 : 0);
            });


        });
    </script>
@endpush
