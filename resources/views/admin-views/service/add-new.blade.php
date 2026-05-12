@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('pooja_add'))

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
                {{ translate('pooja_add') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.service.add-new') }}" method="POST"
            enctype="multipart/form-data" id="services_form">
            @csrf
            <input type="hidden" name="product_type" placeholder="Product Type" value="pooja">
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
                                <div class="form-group col-md-6">
                                    <label class="title-color"for="{{ $lang }}_name">{{ translate('puja_name') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text" {{ $lang == $defaultLanguage ? 'required' : 'required' }}
                                        name="name[]" id="{{ $lang }}_name" class="form-control"
                                        placeholder="Puja Name" pattern="^[A-Za-z\u0900-\u097F\s]+$"
                                        title="Only Hindi/English letters and spaces are allowed.">
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $lang }}_pooja_heading">{{ translate('pooja_heading') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text" {{ $lang == $defaultLanguage ? 'required' : 'required' }}
                                        name="pooja_heading[]" id="{{ $lang }}_pooja_heading" class="form-control"
                                        placeholder="{{ translate('pooja_name_special') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $lang }}_short_benifits">{{ translate('short_benifits') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text" {{ $lang == $defaultLanguage ? 'required' : 'required' }}
                                        name="short_benifits[]" id="{{ $lang }}_short_benifits"
                                        class="form-control" placeholder="{{ translate('short_benifits') }}">
                                </div>

                                <div class="form-group  col-md-6">
                                    <label class="title-color"
                                        for="{{ $lang }}_pooja_venue">{{ translate('pooja_venue') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="pooja_venue[]" id="{{ $lang }}_pooja_venue"
                                        class="form-control" placeholder="{{ translate('puja_venue') }}"
                                        {{ $lang == $defaultLanguage ? 'required' : 'required' }}>
                                </div>

                            </div>
                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="about-pooja-{{ $lang }}-tab"
                                        data-toggle="pill" data-target="#about-pooja-{{ $lang }}" type="button"
                                        role="tab" aria-controls="about-pooja-{{ $lang }}"
                                        aria-selected="true">About Pooja</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="benefits-{{ $lang }}-tab" data-toggle="pill"
                                        data-target="#benefits-{{ $lang }}" type="button" role="tab"
                                        aria-controls="benefits-{{ $lang }}"
                                        aria-selected="false">Benefits</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="process-{{ $lang }}-tab" data-toggle="pill"
                                        data-target="#process-{{ $lang }}" type="button" role="tab"
                                        aria-controls="process-{{ $lang }}" aria-selected="false">Process</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="temple-{{ $lang }}-tab" data-toggle="pill"
                                        data-target="#temple-{{ $lang }}" type="button" role="tab"
                                        aria-controls="temple-{{ $lang }}" aria-selected="false">Temple
                                        Details</button>
                                </li>
                            </ul>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="about-pooja-{{ $lang }}"
                                    role="tabpanel" aria-labelledby="about-pooja-{{ $lang }}-tab">
                                    <label class="title-color"
                                        for="{{ $lang }}_details">{{ translate('about_pooja') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="details[]">{{ old('details') }}</textarea>
                                </div>
                                <div class="tab-pane fade" id="benefits-{{ $lang }}" role="tabpanel"
                                    aria-labelledby="benefits-{{ $lang }}-tab">

                                    <label class="title-color"
                                        for="{{ $lang }}_benefits">{{ translate('benefits') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="benefits[]">{{ old('benefits') }}</textarea>
                                </div>
                                <div class="tab-pane fade" id="process-{{ $lang }}" role="tabpanel"
                                    aria-labelledby="process-{{ $lang }}-tab">

                                    <label class="title-color"
                                        for="{{ $lang }}_process">{{ translate('process') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="process[]">{{ old('process') }}</textarea>
                                </div>
                                <div class="tab-pane fade" id="temple-{{ $lang }}" role="tabpanel"
                                    aria-labelledby="temple-{{ $lang }}-tab">

                                    <label class="title-color"
                                        for="{{ $lang }}_temple_details">{{ translate('temple_details') }}
                                        ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $lang }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="temple_details[]">{{ old('temple_details') }}</textarea>
                                </div>
                            </div>

                        </div>
                    @endforeach


                </div>
            </div>

            {{-- Category Selected Div --}}
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('general_setup') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-visible-city">{{ translate('visible_city') }}</label>
                                <input name="visible_city" id="visible-city" class="form-control" placeholder="Type Your City" required inputmode="text">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-visible-city">{{ translate('is_visible_city') }}</label>
                                <select name="is_visible_city" id="is-visible-cityekdays" class="form-control">
                                    <option value="1">Visible in Same City</option>
                                    <option value="0" selected>Not Visible in Same City</option>
                                </select>
                            </div>
                        </div> --}}
                        <input type="hidden" value="33" name="category_id">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('category') }}</label>
                                <select class="js-select2-custom form-control" name="sub_category_id"
                                    id="sub-category-select" onchange="subCategoryChange(this)">
                                    @foreach ($subCategories as $subCategory)
                                        @if ($subCategory['name'] != 'Vip Pooja' && $subCategory['name'] != 'Anushthan' && $subCategory['name'] != 'Chadhava')
                                            <option value="{{ $subCategory['id'] }}">
                                                {{ $subCategory['name'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color">{{ translate('assign_Pandit') }}*(optional)</label>
                                <select name="pandit_assign" id="assign-pandit"
                                    class=" js-select2-custom assign-pandit form-control">
                                    <option value="">Select Pandit</option>
                                    @foreach ($pandit as $panditji)
                                        <option value="{{ $panditji['id'] }}">{{ $panditji['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color">{{ translate('pooja_type') }}</label>
                                <select name="pooja_type" id="pooja_type" class="pooja_type form-control">
                                    <option selected value="0">Weekly</option>
                                    <option value="1">Special</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4 col-lg-6">
                            <div class="form-group" id="poojaTimeHide">
                                <label class="title-color">{{ translate('pooja_time') }}</label>
                                <input type="text" name="pooja_time" id="pooja_time" class="form-control"
                                    placeholder="Pooja Time" onclick="$timepicker.open()" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group" id="weekDays">
                                <label class="title-color">{{ translate('weeks_days') }}</label>
                                <select name="week_days[]" id="weekdays" class="js-select2-custom form-control"
                                    multiple>
                                    <option disabled>--Select Week Day's</option>
                                    <option value="sunday">Sunday</option>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                </select>
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
                                <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}"
                                    name="tags" data-role="tagsinput">
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
                                    @foreach ($productes as $product)
                                        @if ($product->category_id == 33)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, old('product_id', [])) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- city --}}
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('city_select') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table class="table table-borderless table-hover" id="city-dynamic-field">
                                    <tr>
                                        <td class="pb-0">
                                            <label for="" class="form-label">City Name</label>
                                        </td>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Visibility</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0" style="width: 45%">
                                            <input type="text" name="city[]" class="form-control"
                                                placeholder="Enter city name" required />
                                        </td>
                                        <td class="pt-0" style="width: 45%">
                                            <select class="form-control" name="visibility[]" id="visibility_id">
                                                <option value="0">Not Visible in Same City</option>
                                                <option value="1">Visible in Same City</option>
                                            </select>
                                        </td>
                                        <td class="pt-0" style="width: 10%;">
                                            <button type="button" id="visibility-add" data-visibility-id=""
                                                class="btn btn-primary">
                                                <i>+</i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {{-- Package Price Div --}}
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('package_select') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table class="table table-borderless table-hover" id="package-dynamic-field">
                                    <tr>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Package Name</label>
                                        </td>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Price</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0" style="width: 45%">
                                            <select class="form-control packageIdList" name="packages_id[]"
                                                id="package_id">
                                                <option value="" disabled selected>Select Package</option>
                                                @foreach ($packages as $package)
                                                    <option value="{{ $package->id }}">{{ $package->title }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="pt-0" style="width: 45%">
                                            <input type="number" name="package_price[]" class="form-control" required />
                                        </td>
                                        <td class="pt-0" style="width: 10%;">
                                            <button type="button" id="package-ddadd" data-package-id=""
                                                class="btn btn-primary">
                                                <i>+</i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
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
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <label for="name"
                                                class="title-color text-capitalize font-weight-bold mb-0">{{ translate('service_thumbnail') }}</label>
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

                                <div class="row g-2" id="additional_Image_Section">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom_upload_input position-relative border-dashed-2">
                                            <input type="file" name="images[]"
                                                class="custom-upload-input-file action-add-more-image" data-index="1"
                                                data-imgpreview="additional_Image_1"
                                                accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                data-target-section="#additional_Image_Section">

                                            <span
                                                class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none "
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}"
                                                    alt="">
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
                        <span class="text-info">
                            ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
                    </div>
                    <input type="text" name="video_url"
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
                                <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}"
                                    class="form-control">
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

                                            <span
                                                class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_meta_image_viewer"
                                                    class="h-auto bg-white onerror-add-class-d-none" alt=""
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}">
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
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'add'))
                    <button type="reset" id="reset"
                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                @endif
            </div>
        </form>
    </div>


    <span id="image-path-of-product-upload-icon"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-want-to-add-or-update-this-product"
        data-text="{{ translate('want_to_add_this_product') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-product-added-successfully" data-text="{{ translate('service_added_successfully') }}"></span>
    <span id="system-currency-code" data-value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/astrologer.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    <script>
        $('#pooja_time').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });

        var $timepicker = $('#pooja_time').timepicker({
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
            $('#pooja_type').on('change', function() {
                var selectedValue = $(this).val();
                var dateRequiredSubCategoryId = 1;
                if (selectedValue == dateRequiredSubCategoryId) {
                    $('#weekDays').hide();
                    $('#poojaTimeHide').hide();
                    $('#sub-category-select').val('38').trigger('change');
                } else {
                    $('#weekDays').show();
                    $('#poojaTimeHide').show();
                }
            });
        });
        // Same package are show the error
        $(document).on('change', 'select[name="packages_id[]"]', function() {
            var selectedPackages = [];
            $('select[name="packages_id[]"]').each(function() {
                selectedPackages.push($(this).val());
            });

            $('select[name="packages_id[]"]').each(function() {
                var currentSelect = $(this);
                currentSelect.find('option').each(function() {
                    var option = $(this);
                    if (selectedPackages.includes(option.val()) && option.val() !== currentSelect
                        .val()) {
                        option.prop('disabled', true);
                    } else {
                        option.prop('disabled', false);
                    }
                });
            });
        });
    </script>
    {{-- sub category change --}}
    <script>
        function subCategoryChange(that) {
            var subCategory = $(that).val();
            $('#pooja_type').html('');
            if (subCategory == 38) {
                $('#pooja_type').append(`<option value="1">Special</option>`);
                $('#weekDays').hide();
                $('#poojaTimeHide').hide();
            } else {
                $('#pooja_type').append(`<option selected value="0">Weekly</option><option value="1">Special</option>`);
                $('#weekDays').show();
                $('#poojaTimeHide').show();
            }
        }
    </script>

    {{-- pooja visible city --}}
    <script>
        let cityIncrement = 1;
        $(document).on('click', '#visibility-add', function(e) {
            e.preventDefault();
            var cityInput = $('input[name="city[]"]');
            if (cityInput.val().trim() === '') {
                toastr.error('Please enter city name.');
                return;
            }

            cityIncrement++;
            var selectedCity = [];
            $('select[name="visibility[]"]').each(function() {
                selectedCity.push($(this).val());
            });

            var html = `
                    <tr id="city-row${cityIncrement}">
                        <td><input type="text" name="city[]" class="form-control" placeholder="Enter city name" required /></td>
                        <td>
                            <select class="form-control" name="visibility[]"
                                id="visibility_id">
                                <option value="0">Not Visible in Same City</option>
                                <option value="1">Visible in Same City</option>
                            </select>
                        </td>
                        <td><button type="button" name="remove" id="${cityIncrement}" class="btn btn-danger city-btn-remove">x</button></td>
                    </tr>
                `;
            $('#city-dynamic-field').append(html);
        });

        $(document).on('click', '.city-btn-remove', function() {
            var button_id = $(this).attr("id");
            $('#city-row' + button_id + '').remove();
        });
    </script>
@endpush
