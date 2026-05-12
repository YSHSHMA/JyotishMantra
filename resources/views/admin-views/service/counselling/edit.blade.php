@extends('layouts.back-end.app')

@section('title', translate('counselling_Update'))

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
                {{ translate('counselling_Update') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.service.counselling.update', $service['id']) }}"
            method="post" enctype="multipart/form-data" id="service_form">
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
                        if (count($service['translations'])) {
                            $translate = [];
                            foreach ($service['translations'] as $translation) {
                                if ($translation->locale == $language && $translation->key == 'name') {
                                    $translate[$language]['name'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'description') {
                                    $translate[$language]['description'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'process') {
                                    $translate[$language]['process'] = $translation->value;
                                }
                            }
                        }
                        ?>
                        <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $language }}-form">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_name">{{ translate('service_name') }}
                                    ({{ strtoupper($language) }})
                                </label>
                                <input type="text" {{ $language == 'en' ? 'required' : '' }} name="name[]"
                                    id="{{ $language }}_name"
                                    value="{{ $translate[$language]['name'] ?? $service['name'] }}" class="form-control"
                                    placeholder="{{ translate('service_name') }}" required>
                            </div>
                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="about-pooja-{{ $language }}-tab"
                                        data-toggle="pill" data-target="#about-pooja-{{ $language }}" type="button"
                                        role="tab" aria-controls="about-pooja-{{ $language }}"
                                        aria-selected="true">About Pooja</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="process-{{ $language }}-tab" data-toggle="pill"
                                        data-target="#process-{{ $language }}" type="button" role="tab"
                                        aria-controls="process-{{ $language }}" aria-selected="false">Process</button>
                                </li>
                            </ul>
                            <input type="hidden" name="lang[]" value="{{ $language }}">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="about-pooja-{{ $language }}" role="tabpanel"
                                    aria-labelledby="about-pooja-{{ $language }}-tab">
                                    <label class="title-color"
                                        for="{{ $language }}_description">{{ translate('about_pooja') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="description[]">{!! $translate[$language]['description'] ?? $service['details'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="process-{{ $language }}" role="tabpanel"
                                    aria-labelledby="process-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_process">{{ translate('process') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="process[]">{!! $translate[$language]['process'] ?? $service['process'] !!}</textarea>
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
                        <input type="hidden" value="39" name="category_id">
                        {{-- <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('category') }}</label>
                                <select class="js-example-basic-multiple js-states js-example-responsive form-control action-get-request-onchange"
                                    name="category_id"
                                    id="category_id"
                                    data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                    data-element-id="sub-category-select"
                                    data-element-type="select">
                                    <option value="0" selected disabled>---{{ translate('select') }}---</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id']}}" {{ $category->id==$service['category_id'] ? 'selected' : ''}}>{{ $category['defaultName']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        {{-- <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="title-color">{{ translate('sub_Category') }}</label>
                                <select
                                    class="js-example-basic-multiple js-states js-example-responsive form-control action-get-request-onchange"
                                    name="sub_category_id" id="sub-category-select"
                                    data-id="{{ $service['sub_category_id'] }}"
                                    data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                    data-element-id="sub-sub-category-select"
                                    data-element-type="select">
                                </select>
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="title-color">{{ translate('sub_Sub_Category') }}</label>
                                <select
                                    class="js-example-basic-multiple js-states js-example-responsive form-control"
                                    data-id="{{ $service['sub_sub_category_id'] }}"
                                    name="sub_sub_category_id" id="sub-sub-category-select">
                                </select>
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('main_price') }}</label>
                                <input type="number" name="counselling_main_price" id="main-price" class="form-control"
                                    placeholder="Main Price" value="{{ $service['counselling_main_price'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('counselling_selling_price') }}</label>
                                <input type="number" name="counselling_selling_price" id="selling-price"
                                    class="form-control" placeholder="Selling Price"
                                    value="{{ $service['counselling_selling_price'] }}" required>
                                    <p id="selling-price-validate" class="text-danger" style="display: none;">selling Price can not greater than main price</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('category') }}</label>
                                <select class="js-select2-custom form-control" name="sub_category_id"
                                    id="sub-category-select">
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory['id'] }}"
                                            {{ $service['sub_category_id'] == $subCategory['id'] ? 'selected' : '' }}>
                                            {{ $subCategory['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-6" id="digital_file_ready_show">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label for="digital_file_ready"
                                        class="title-color mb-0">{{ translate('upload_file') }}</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                        title="{{ translate('upload_the_digital_products_from_here') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                            alt="">
                                    </span>
                                </div>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="digital_file_ready"
                                            id="digital_file_ready" aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label"
                                            for="digital_file_ready">{{ translate('choose_file') }}</label>
                                    </div>
                                </div>

                                <div class="mt-2" style="font-size: 13px;">
                                    {{ translate('file_type') . ': jpg, jpeg, png, gif, zip, pdf' }}</div>
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">{{ translate('product_name') }}
                                </label>
                                <select class="js-select2-custom form-control" name="product_id[]" multiple>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ $service['product_id'] != 'null' && in_array($product->id, json_decode($service['product_id'], true)) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
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
                                    value="@foreach ($service->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                    data-role="tagsinput">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="mt-3 rest-part">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name"
                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('service_thumbnail') }}</label>
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

                                        @if (File::exists(base_path('storage/app/public/pooja/thumbnail/' . $service->thumbnail)))
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
                                                src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $service->thumbnail, type: 'backend-product') }}">
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
                                <p class="text-muted">{{ translate('upload_additional_service_images') }}</p>
                                <div class="coba-area">

                                    <div class="row g-2" id="additional_Image_Section">

                                        @if (is_array($service['images']) && count($service['images']) == 0)
                                            @foreach (json_decode($service['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.service.delete-image', ['id' => $service['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}" alt=""
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
                                            @foreach (json_decode($service['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.service.delete-image', ['id' => $service['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}" alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo, type: 'backend-product') }}">
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

                <input type="hidden" id="images" value="{{ $service->images }}">
                <input type="hidden" id="service_id" value="{{ $service->id }}">
                {{-- <input type="hidden" id="remove_url" value="{{ route('admin.products.delete-image') }}"> --}}
            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('service_video') }}</h4>
                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                            title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}.">
                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                alt="">
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="title-color mb-0">{{ translate('youtube_video_link') }}</label>
                        <span class="text-info"> ( {{ translate('optional_please_provide_embed_link_not_direct_link') }}.
                            )</span>
                    </div>
                    <input type="text" value="{{ $service['video_url'] }}" name="video_url"
                        placeholder="{{ translate('ex') . ': https://www.youtube.com/embed/5R06LRdUCSE' }}"
                        class="form-control">
                </div>
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
                                <input type="text" name="meta_title" value="{{ $service['meta_title'] }}"
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
                                <textarea rows="4" type="text" name="meta_description" class="form-control">{{ $service['meta_description'] }}</textarea>
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

                                            @if (File::exists(base_path('storage/app/public/pooja/meta/' . $service['meta_image'])))
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
                                                    src="{{ getValidImage(path: 'storage/app/public/product/meta/' . $service['meta_image'], type: 'backend-banner') }}">
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
                <button type="submit" id="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
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
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script>
        initSample();
    </script>
    <script>
        "use strict";


        let imageCount = {{ 15 - count(json_decode($service->images)) }};
        let thumbnail =
            '{{ productImagePath('thumbnail') . '/' . $service->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}';
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
                    image: '{{ productImagePath('thumbnail') . '/' . $service->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}',
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
                let category = $("#category_id").val();
                let sub_category = $("#sub-category-select").attr("data-id");
                let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
                getRequestFunctionality('{{ route('admin.service.get-categories') }}?parent_id=' +
                    category + '&sub_category=' + sub_category, 'sub-category-select', 'select');
                getRequestFunctionality('{{ route('admin.service.get-categories') }}?parent_id=' +
                    sub_category + '&sub_category=' + sub_sub_category, 'sub-sub-category-select',
                    'select');
            }, 100)
        });
    </script>

    {{-- selling price validate --}}
    <script>
        $('#selling-price').keyup(function (e) { 
            var mainPrice = parseInt($('#main-price').val());
            var sellingPrice = parseInt($(this).val());
            if(sellingPrice>mainPrice){
                $('#selling-price-validate').show();
                $('#submit').prop('disabled',true);
            }else{
                $('#selling-price-validate').hide();
                $('#submit').prop('disabled',false);
            }
        });
    </script>
@endpush
