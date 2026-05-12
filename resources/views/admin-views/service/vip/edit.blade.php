@extends('layouts.back-end.app')

@section('title', translate('VIP_Pooja|Edit'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
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
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('VIP_Pooja|Edit') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.service.vip.update', $vip['id']) }}" method="post"
            enctype="multipart/form-data" id="service_form">
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
                        if (count($vip['translations'])) {
                            $translate = [];
                            foreach ($vip['translations'] as $translation) {
                                if ($translation->locale == $language && $translation->key == 'name') {
                                    $translate[$language]['name'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'short_benifits') {
                                    $translate[$language]['short_benifits'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'details') {
                                    $translate[$language]['details'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'benefits') {
                                    $translate[$language]['benefits'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'process') {
                                    $translate[$language]['process'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'temple_details') {
                                    $translate[$language]['temple_details'] = $translation->value;
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
                                    <label class="title-color" for="{{ $language }}_name">{{ translate('puja_name') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="name[]"
                                        id="{{ $language }}_name"
                                        value="{{ $translate[$language]['name'] ?? $vip['name'] }}" class="form-control"
                                        placeholder="{{ translate('puja_name') }}" required pattern="^[A-Za-z\u0900-\u097F\s]+$"
                                            title="Only Hindi/English letters and spaces are allowed.">
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_pooja_heading">{{ translate('pooja_heading') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="pooja_heading[]" id="{{ $language }}_pooja_heading"
                                        value="{{ $translate[$language]['pooja_heading'] ?? $vip['pooja_heading'] }}"
                                        class="form-control" placeholder="{{ translate('pooja_heading') }}" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label
                                        class="title-color"for="{{ $language }}_short_benifits">{{ translate('short_benifits') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : '' }} name="short_benifits[]"
                                        id="{{ $language }}_short_benifits"
                                        value="{{ $translate[$language]['short_benifits'] ?? $vip['short_benifits'] }}"
                                        class="form-control" placeholder="{{ translate('short_benifits') }}" required>
                                </div>


                            </div>
                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="about-pooja-{{ $language }}-tab"
                                        data-toggle="pill" data-target="#about-pooja-{{ $language }}" type="button"
                                        role="tab" aria-controls="about-pooja-{{ $language }}"
                                        aria-selected="true">About Puja</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="benefits-{{ $language }}-tab" data-toggle="pill"
                                        data-target="#benefits-{{ $language }}" type="button" role="tab"
                                        aria-controls="benefits-{{ $language }}"
                                        aria-selected="false">Benefits</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="process-{{ $language }}-tab" data-toggle="pill"
                                        data-target="#process-{{ $language }}" type="button" role="tab"
                                        aria-controls="process-{{ $language }}" aria-selected="false">Process</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="temple-{{ $language }}-tab" data-toggle="pill"
                                        data-target="#temple-{{ $language }}" type="button" role="tab"
                                        aria-controls="temple-{{ $language }}" aria-selected="false">Temple
                                        Details</button>
                                </li>
                            </ul>
                            <input type="hidden" name="lang[]" value="{{ $language }}">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="about-pooja-{{ $language }}"
                                    role="tabpanel" aria-labelledby="about-pooja-{{ $language }}-tab">
                                    <label class="title-color"
                                        for="{{ $language }}_description">{{ translate('about_puja') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="details[]">{!! $translate[$language]['details'] ?? $vip['details'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="benefits-{{ $language }}" role="tabpanel"
                                    aria-labelledby="benefits-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_benefits">{{ translate('benefits') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="benefits[]">{!! $translate[$language]['benefits'] ?? $vip['benefits'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="process-{{ $language }}" role="tabpanel"
                                    aria-labelledby="process-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_process">{{ translate('process') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="process[]">{!! $translate[$language]['process'] ?? $vip['process'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="temple-{{ $language }}" role="tabpanel"
                                    aria-labelledby="temple-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_temple_details">{{ translate('temple_details') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" name="temple_details[]">{!! $translate[$language]['temple_details'] ?? $vip['temple_details'] !!}</textarea>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_anushthan"
                                        name="is_anushthan" value="1"
                                        {{ $vip['is_anushthan'] == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label text-muted" for="is_anushthan">
                                        {{ translate('is_anushthan') }}
                                    </label>
                                    {{-- <input type="hidden" id="is_anushthan_hidden" name="is_anushthan"
                                        value="{{ $vip['is_anushthan'] == 1 ? '1' : '0' }}"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                    value="@foreach ($vip->tags as $c) {{ $c->tag . ',' }} @endforeach"
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
                                        $vipProduct = array_unique(json_decode($vip['product_id'], true));
                                    @endphp
                                    @foreach ($productes as $product)
                                        @if ($product->category_id == 33 && in_array($product->id, $vipProduct))
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
                                @php
                                    $packageGetEdit = json_decode($vip['packages_id']);
                                @endphp
                                <table class="table table-borderless table-hover" id="package-update-dynamic-field-vip">
                                    <tr>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Package Name</label>
                                        </td>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Price</label>
                                        </td>
                                    </tr>
                                    @if (empty($packageGetEdit))
                                        <tr>
                                            <td class="pt-0" style="width: 45%">
                                                <select class="form-control" name="packages_id[]" id="package_id">
                                                    <option value="" disabled>Select Package</option>
                                                    @foreach ($packages as $package)
                                                        @if (in_array($package->id, [5, 6]))
                                                            <option value="{{ $package->id }}" class="package-option"
                                                                data-visible="0">{{ $package->title }}</option>
                                                        @elseif (in_array($package->id, [7, 8]))
                                                            <option value="{{ $package->id }}" class="package-option"
                                                                data-visible="1">{{ $package->title }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="pt-0" style="width: 45%">
                                                <input type="number" name="package_price[]" class="form-control" />
                                            </td>
                                            <td class="pt-0" style="width: 10%;">
                                                <button type="button" id="package-update"
                                                    class="btn btn-primary"><i>+</i></button>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($packageGetEdit as $Packageskey => $pac)
                                            <tr id="package-update-row-vip{{ $Packageskey + 1 }}">
                                                <td class="pt-0" style="width: 45%">
                                                    <select class="form-control" name="packages_id[]" id="package_id">
                                                        <option value="" disabled>Select Package</option>
                                                        @foreach ($packages as $package)
                                                            @if (in_array($package->id, [5, 6]))
                                                                <option value="{{ $package->id }}"
                                                                    {{ $package->id == $pac->package_id ? 'selected' : '' }}>
                                                                    {{ $package->title }}
                                                                </option>
                                                            @elseif (in_array($package->id, [7, 8]))
                                                                <option value="{{ $package->id }}"
                                                                    {{ $package->id == $pac->package_id ? 'selected' : '' }}>
                                                                    {{ $package->title }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="pt-0" style="width: 45%">
                                                    <input type="number" name="package_price[]"
                                                        class="form-control"value="{{ $pac->package_price }}" />
                                                </td>
                                                @if ($loop->first)
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="package-update-add-vip"
                                                            class="btn btn-primary"><i>+</i></button>
                                                    </td>
                                                @else
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="{{ $Packageskey + 1 }}"
                                                            class="btn btn-danger package-update-btn-remove-vip"><i>x</i></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                                <span class="m-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                        alt="" class="ripple-animation">
                                    <strong> Note: "If both 'VIP Anushthan' and 'Instance Anusthan' packages are selected,
                                        ensure the 'Anushthan' checkbox is checked. If either of these packages is not
                                        selected, the checkbox should display 'VIP Pooja'."</strong></span>

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
                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Vip _Pooja_thumbnail') }}</label>
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

                                        @if (File::exists(base_path('storage/app/public/pooja/vip/thumbnail/' . $vip->thumbnail)))
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
                                                src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $vip->thumbnail, type: 'backend-product') }}">
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

                                        @if (is_array($vip['images']) && count($vip['images']) == 0)
                                            @foreach (json_decode($vip['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.service.vip.delete-image', ['id' => $vip['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/vip/' . $photo, type: 'backend-product') }}">
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
                                            @foreach (json_decode($vip['images']) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))

                                                <div class="col-sm-12 col-md-4">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('admin.service.vip.delete-image', ['id' => $vip['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/vip/' . $photo, type: 'backend-product') }}">
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

                <input type="hidden" id="images" value="{{ $vip->images }}">
                <input type="hidden" id="vip_id" value="{{ $vip->id }}">
                <input type="hidden" id="remove_url" value="{{ route('admin.service.vip.delete-image') }}">
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
                    <input type="text" value="{{ $vip['video_url'] }}" name="video_url"
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
                                <input type="text" name="meta_title" value="{{ $vip['meta_title'] }}"
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
                                <textarea rows="4" type="text" name="meta_description" class="form-control">{{ $vip['meta_description'] }}</textarea>
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

                                            @if (File::exists(base_path('storage/app/public/pooja/vip/meta/' . $vip['meta_image'])))
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
                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/vip/meta/' . $vip['meta_image'], type: 'backend-banner') }}">
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
    <span id="message-want-to-add-or-update-this-vip" data-text="{{ translate('want_to_update_this_vip') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-vip-added-successfully" data-text="{{ translate('vip_added_successfully') }}"></span>
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


        let imageCount = {{ 15 - count(json_decode($vip->images)) }};
        let thumbnail =
            '{{ productImagePath('thumbnail') . '/' . $vip->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}';
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
                    image: '{{ productImagePath('thumbnail') . '/' . $vip->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}',
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
    <script>
        $(document).ready(function() {
            // var packagesData = JSON.parse({!! json_encode($vip['packages_id']) !!});

            var selectedPackages = [];
            $('select[name="packages_id[]"]').each(function() {
                selectedPackages.push($(this).val());
            });
            $("#package-update-add-vip").on('click', function(e) {
                // packagesUpdateIncrementVIP++;
                var packagesUpdateIncrementVIP = $('#package-update-dynamic-field-vip ').length + 1;
                console.log(packagesUpdateIncrementVIP);
                $.ajax({
                    url: "{{ url('admin/service/get-packages-dropdown-vip') }}",
                    method: 'GET',
                    data: {
                        packageIds: selectedPackages
                    },
                    success: function(response) {
                        console.log(response);
                        var html = `
                        <tr id="package-update-row-vip${packagesUpdateIncrementVIP}">
                            <td>${response.html}</td>
                            <td><input type="number" name="package_price[]" class="form-control"></td>
                            <td><button type="button" name="remove" id="${packagesUpdateIncrementVIP}" class="btn btn-danger package-update-btn-remove-vip">x</button></td>
                        </tr>
                    `;
                        $('#package-update-dynamic-field-vip').append(html);
                    },
                });
            });

            $(document).on('click', '.package-update-btn-remove-vip', function() {
                var button_id = $(this).attr("id");
                $('#package-update-row-vip' + button_id + '').remove();
            });
        });
    </script>
    <script>
        document.getElementById('is_anushthan').addEventListener('change', function() {
            var hiddenInput = '';
            var hiddenInput = document.getElementById('is_anushthan_hidden');
            var packageSelect = document.getElementById('package_id');
            hiddenInput.value = this.checked ? '1' : '0';
            packageSelect.innerHTML = '<option value="" disabled selected>Select Package</option>';
            foreach($packages as $package) {
                if (in_array($package - > id, [5, 6]) && $_POST['hiddenInput'] == '0') {
                    var option = document.createElement('option');
                    option.value = '{{ $package->id }}';
                    option.textContent = '{{ $package->title }}';
                    packageSelect.appendChild(option);
                }
                elseif(in_array($package - > id, [7, 8]) && hiddenInput.value == '1') {}
                var option = document.createElement('option');
                option.value = '{{ $package->id }}';
                option.textContent = '{{ $package->title }}';
                packageSelect.appendChild(option);
            }

        });
    </script>
@endpush
