{{-- @php
    dd($service);
@endphp --}}
@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('pooja_edit'))

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
                {{ translate('pooja_edit') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.service.update', $service['id']) }}" method="post"
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
                        if (count($service['translations'])) {
                            $translate = [];
                            foreach ($service['translations'] as $translation) {
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
                                if ($translation->locale == $language && $translation->key == 'pooja_venue') {
                                    $translate[$language]['pooja_venue'] = $translation->value;
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
                                    <input type="text" {{ $language == 'en' ? 'required' : 'required' }} name="name[]"
                                        id="{{ $language }}_name"
                                        value="{{ $translate[$language]['name'] ?? $service['name'] }}"
                                        class="form-control" placeholder="{{ translate('puja_name') }}" pattern="^[A-Za-z\u0900-\u097F\s]+$"
                                            title="Only Hindi/English letters and spaces are allowed." required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_pooja_heading">{{ translate('pooja_heading') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="pooja_heading[]" id="{{ $language }}_pooja_heading"
                                        value="{{ $translate[$language]['pooja_heading'] ?? $service['pooja_heading'] }}"
                                        class="form-control" placeholder="{{ translate('pooja_heading') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label
                                        class="title-color"for="{{ $language }}_short_benifits">{{ translate('short_benifits') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                    <input type="text" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="short_benifits[]" id="{{ $language }}_short_benifits"
                                        value="{{ $translate[$language]['short_benifits'] ?? $service['short_benifits'] }}"
                                        class="form-control" placeholder="{{ translate('short_benifits') }}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label  class="title-color"for="{{ $language }}_pooja_venue">{{ translate('pooja_venue') }}
                                        ({{ strtoupper($language) }})
                                    </label>
                                   
                                <input type="text"  name="pooja_venue[]"  id="{{ $language }}_pooja_venue" class="form-control" 
                                       placeholder="Pooja Venue"   value="{{ $translate[$language]['pooja_venue'] ?? $service['pooja_venue'] }}"   {{ $language == 'en' ? 'required' : 'required' }}>                             
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
                                    <textarea class="ckeditor" id="editor{{ $language }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="details[]">{!! $translate[$language]['details'] ?? $service['details'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="benefits-{{ $language }}" role="tabpanel"
                                    aria-labelledby="benefits-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_benefits">{{ translate('benefits') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="benefits[]">{!! $translate[$language]['benefits'] ?? $service['benefits'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="process-{{ $language }}" role="tabpanel"
                                    aria-labelledby="process-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_process">{{ translate('process') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="process[]">{!! $translate[$language]['process'] ?? $service['process'] !!}</textarea>
                                </div>
                                <div class="tab-pane fade" id="temple-{{ $language }}" role="tabpanel"
                                    aria-labelledby="temple-{{ $language }}-tab">

                                    <label class="title-color"
                                        for="{{ $language }}_temple_details">{{ translate('temple_details') }}
                                        ({{ strtoupper($language) }})</label>
                                    <textarea class="ckeditor" id="editor{{ $language }}" {{ $language == 'en' ? 'required' : 'required' }}
                                        name="temple_details[]">{!! $translate[$language]['temple_details'] ?? $service['temple_details'] !!}</textarea>
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
                        {{-- <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-visible-city">{{ translate('visible_city') }}</label>
                                <input name="visible_city" id="visible-city" class="form-control" placeholder="Type Your City" required inputmode="text" value="{{ $service['visible_city'] }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group">
                                <label class="title-visible-city">{{ translate('is_visible_city') }}</label>
                                <select name="is_visible_city" id="is-visible-cityekdays" class="form-control">
                                    <option value="1" {{ (int)$service['is_visible_city'] === 1 ? 'selected' : '' }}>Visible in Same City</option>
                                    <option value="0" {{ (int)$service['is_visible_city'] === 0 ? 'selected' : '' }}>Not Visible in Same City</option>
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
                                            <option value="{{ $subCategory['id'] }}"
                                                {{ $service['sub_category_id'] == $subCategory['id'] ? 'selected' : '' }}>
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
                                <select name="pandit_assign" id="assign-pandit" class="assign-pandit form-control">
                                    <option value="">Select Pandit</option>
                                    @foreach ($pandit as $panditji)
                                        <option value="{{ $panditji['id'] }}"
                                            {{ $service['pandit_assign'] == $panditji['id'] ? 'selected' : '' }}>
                                            {{ $panditji['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color">{{ translate('pooja_type') }}</label>
                                <select name="pooja_type" id="pooja_type" class="form-control">
                                    <option value="0" {{ $service['pooja_type'] == 0 ? 'selected' : '' }}>Weekly</option>
                                    <option value="1" {{ $service['pooja_type'] == 1 ? 'selected' : '' }}>Special</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group" id="poojaTimeHide">
                                <label class="title-color">{{ translate('puja_time') }}</label>
                                <input type="text" name="pooja_time" id="pooja_time" class="form-control"
                                    placeholder="Puja Time"
                                    value="{{ isset($service['pooja_time']) ? date('H:i', strtotime($service['pooja_time'])) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6">
                            <div class="form-group" id="weekDays">
                                @php
                                    $weekDays =
                                        isset($service['week_days']) && !empty($service['week_days'])
                                            ? json_decode($service['week_days'], true)
                                            : [];
                                    // dd(is_array($weekDays));
                                @endphp
                                <label class="title-color">{{ translate('weeks_days') }}</label>
                                <select name="week_days[]" id="weekdays" class="js-select2-custom form-control"
                                    multiple>
                                    <option value="0" disabled {{ empty($weekDays) ? 'selected' : '' }}>
                                        ---{{ translate('select') }}---</option>
                                    <option value="sunday"
                                        {{ is_array($weekDays) && in_array('sunday', $weekDays) ? 'selected' : '' }}>Sunday
                                    </option>
                                    <option value="monday"
                                        {{ is_array($weekDays) && in_array('monday', $weekDays) ? 'selected' : '' }}>Monday
                                    </option>
                                    <option value="tuesday"
                                        {{ is_array($weekDays) && in_array('tuesday', $weekDays) ? 'selected' : '' }}>
                                        Tuesday</option>
                                    <option value="wednesday"
                                        {{ is_array($weekDays) && in_array('wednesday', $weekDays) ? 'selected' : '' }}>
                                        Wednesday</option>
                                    <option value="thursday"
                                        {{ is_array($weekDays) && in_array('thursday', $weekDays) ? 'selected' : '' }}>
                                        Thursday</option>
                                    <option value="friday"
                                        {{ is_array($weekDays) && in_array('friday', $weekDays) ? 'selected' : '' }}>Friday
                                    </option>
                                    <option value="saturday"
                                        {{ is_array($weekDays) && in_array('saturday', $weekDays) ? 'selected' : '' }}>
                                        Saturday</option>
                                </select>


                            </div>
                            {{-- <div id="date-input-container" style="display: none;">
                                <div class="form-group">
                                    <label for="event-date">{{ translate('event_date') }}</label>
                                    <input type="text" id="event-date" name="event_date" class="form-control"
                                        value="{{ date('d/m/Y', strtotime($service['event_date'])) }}">
                                </div>
                            </div> --}}
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
                                    value="@foreach ($service->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                    data-role="tagsinput">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label
                                    class="title-color d-flex align-items-center gap-2">{{ translate('charity_product') }}
                                </label>
                                <select class="js-select2-custom form-control"
                                    name="product_id[]" data-element-id="sub-category-select" data-element-type="select"
                                    required multiple>
                                    <option value="" disabled>Select Product</option>
                                    @php
                                        $ServiceProduct = array_unique(json_decode($service['product_id'], true));
                                    @endphp
                                    @foreach ($productes as $product)
                                        @if ($product->category_id == 33 && in_array($product->id, $ServiceProduct))
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
                                @php
                                    $cityGetEdit = json_decode($service['visible_city']);
                                @endphp
                                <table class="table table-borderless table-hover" id="city-update-dynamic-field">
                                    <tr>
                                        <td class="pb-0">
                                            <label for="" class="form-label">City Name</label>
                                        </td>
                                        <td class="pb-0">
                                            <label for="" class="form-label">Visibility</label>
                                        </td>
                                    </tr>
                                    @if (empty($cityGetEdit))
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
                                                <button type="button" id="city-update"
                                                    class="btn btn-primary"><i>+</i></button>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($cityGetEdit as $citykey => $city)
                                            <tr id="city-update-row{{ $citykey + 1 }}">
                                                <td class="pt-0" style="width: 45%">
                                                    <input type="text" name="city[]" class="form-control"
                                                placeholder="Enter city name" value="{{ $city->city }}"/>
                                                </td>
                                                <td class="pt-0" style="width: 45%">
                                                    <select class="form-control" name="visibility[]" id="visibility_id">
                                                        <option value="0" {{ 0 == $city->visibility ? 'selected' : '' }}>Not Visible in Same City</option>
                                                        <option value="1" {{ 1 == $city->visibility ? 'selected' : '' }}>Visible in Same City</option>
                                                    </select>
                                                </td>
                                                @if ($loop->first)
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="city-update-add"
                                                            class="btn btn-primary"><i>+</i></button>
                                                    </td>
                                                @else
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="{{ $citykey + 1 }}"
                                                            class="btn btn-danger city-update-btn-remove"><i>x</i></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
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
                                @php
                                    $packageGetEdit = json_decode($service['packages_id']);
                                @endphp
                                <table class="table table-borderless table-hover" id="package-update-dynamic-field">
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
                                                        <option value="{{ $package->id }}">{{ $package->title }}
                                                        </option>
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
                                            <tr id="package-update-row{{ $Packageskey + 1 }}">
                                                <td class="pt-0" style="width: 45%">
                                                    <select class="form-control" name="packages_id[]" id="package_id">
                                                        <option value="" disabled>Select Package</option>
                                                        @foreach ($packages as $package)
                                                            <option value="{{ $package->id }}"
                                                                {{ $package->id == $pac->package_id ? 'selected' : '' }}>
                                                                {{ $package->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="pt-0" style="width: 45%">
                                                    <input type="number" name="package_price[]"
                                                        class="form-control"value="{{ $pac->package_price }}" />
                                                </td>
                                                @if ($loop->first)
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="package-update-add"
                                                            class="btn btn-primary"><i>+</i></button>
                                                    </td>
                                                @else
                                                    <td class="pt-0" style="width: 10%;">
                                                        <button type="button" id="{{ $Packageskey + 1 }}"
                                                            class="btn btn-danger package-update-btn-remove"><i>x</i></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
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
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
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
                <input type="hidden" id="remove_url"
                    value="{{ route('admin.service.delete-image', ['id' => $service['id'], 'name' => $photo]) }}">
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
                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/meta/' . $service['meta_image'], type: 'backend-banner') }}">
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
                @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'edit'))
                <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
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
    
    {{-- city --}}
    <script>
        // city add
        let cityIncrement = 1;
        $(document).on('click', '#city-update', function(e) {
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
            $('#city-update-dynamic-field').append(html);
        });

        $(document).on('click', '.city-btn-remove', function() {
            var button_id = $(this).attr("id");
            $('#city-row' + button_id + '').remove();
        });

        // city update
        $(document).ready(function() {
            $("#city-update-add").on('click', function(e) {
                var selectedCity = [];
                $('select[name="visibility[]"]').each(function() {
                    selectedCity.push($(this).val());
                });
                var cityInput = $('input[name="city[]"]');
                if (cityInput.val().trim() === '') {
                    toastr.error('Please enter city name.');
                    return;
                }
                var cityUpdateIncrement = $('#city-update-dynamic-field tr').length - 1;
                // $.ajax({
                //     url: "{{ url('admin/service/get-packages-dropdown') }}",
                //     method: 'GET',
                //     data: {
                //         packageIds: selectedPackages
                //     },
                    // success: function(response) {
                    //     console.log(response);
                        var html = `
                            <tr id="city-update-row${cityUpdateIncrement}">
                                <td><input type="text" name="city[]" class="form-control" placeholder="Enter city name" required /></td>
                                <td>
                                    <select class="form-control" name="visibility[]"
                                        id="visibility_id">
                                        <option value="0">Not Visible in Same City</option>
                                        <option value="1">Visible in Same City</option>
                                    </select>
                                </td>
                                <td><button type="button" name="remove" id="${cityUpdateIncrement}" class="btn btn-danger city-update-btn-remove">x</button></td>
                            </tr>
                        `;
                        $('#city-update-dynamic-field').append(html);
                    // },
                // });
            });

            $(document).on('click', '.city-update-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#city-update-row' + button_id + '').remove();
            });
        });
    </script>

    {{-- package update --}}
    <script>
        $(document).ready(function() {
            // var packagesData = JSON.parse({!! json_encode($service['packages_id']) !!});

            $("#package-update-add").on('click', function(e) {
                var selectedPackages = [];
                $('select[name="packages_id[]"]').each(function() {
                    selectedPackages.push($(this).val());
                });
                var cityInput = $('input[name="package_price[]"]').last();
                if (lastPriceInput.val() === '') {
                    toastr.error('Please enter a valid price for the selected package.');
                    return;
                }
                // packagesUpdateIncrement++;
                var packagesUpdateIncrement = $('#package-update-dynamic-field tr').length - 1;
                $.ajax({
                    url: "{{ url('admin/service/get-packages-dropdown') }}",
                    method: 'GET',
                    data: {
                        packageIds: selectedPackages
                    },
                    success: function(response) {
                        console.log(response);
                        var html = `
                        <tr id="package-update-row${packagesUpdateIncrement}">
                            <td>${response.html}</td>
                            <td><input type="number" name="package_price[]" class="form-control"></td>
                            <td><button type="button" name="remove" id="${packagesUpdateIncrement}" class="btn btn-danger package-update-btn-remove">x</button></td>
                        </tr>
                    `;
                        $('#package-update-dynamic-field').append(html);
                    },
                });
            });

            $(document).on('click', '.package-update-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#package-update-row' + button_id + '').remove();
            });
        });
    </script>
    <script>
       $(document).ready(function() {
            const dateRequiredSubCategoryId = 1; 
            if ($('#pooja_type').val() == dateRequiredSubCategoryId) {
                $('#weekDays').hide();
                $('#poojaTimeHide').hide();
                $('#sub-category-select').val('38').trigger('change');
            } else {
                $('#weekDays').show();
                $('#poojaTimeHide').show();
            }

            $('#pooja_type').on('change', function() {
                var selectedValue = $(this).val();
                
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
@endpush
