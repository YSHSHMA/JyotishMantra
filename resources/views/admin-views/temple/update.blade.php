@extends('layouts.back-end.app')

@section('title', translate('temple_edit'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">

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
            {{ translate('temple_edit') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.temple.update', $temple['id']) }}" method="post" enctype="multipart/form-data" id="product_form">
        @csrf

        <div class="card">
            <div class="px-4 pt-3">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    @foreach ($languages as $language)
                    <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab  {{ $language == $defaultLanguage ? 'active' : '' }}" href="#" id="{{ $language }}-link">{{ getLanguageName($language) . '(' . strtoupper($language) . ')' }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body">
                <input type="hidden" name="longitude" value="{{ $temple['longitude'] }}">
                <input type="hidden" name="latitude" value="{{ $temple['latitude'] }}">
                @foreach ($languages as $keys=>$language)
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
                        if ($translation->locale == $language && $translation->key == 'temple_known') {
                            $translate[$language]['temple_known'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == 'expect_details') {
                            $translate[$language]['expect_details'] = $translation->value;
                        }

                        if ($translation->locale == $language && $translation->key == 'tips_details') {
                            $translate[$language]['tips_details'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == 'temple_services') {
                            $translate[$language]['temple_services'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == 'temple_aarti') {
                            $translate[$language]['temple_aarti'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == 'tourist_place') {
                            $translate[$language]['tourist_place'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == 'temple_local_food') {
                            $translate[$language]['temple_local_food'] = $translation->value;
                        }
                    }
                }
                ?>
                <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form" id="{{ $language }}-form">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="title-color" for="{{ $language }}_name">{{ translate('temple_name') }}
                                ({{ strtoupper($language) }})
                            </label>
                            <input type="text" {{ $language == 'en' ? 'required' : '' }} name="name[]" id="{{ $language }}_name" value="{{ $translate[$language]['name'] ?? $temple['name'] }}" class="form-control getAddress_google" placeholder="{{ translate('new_temple') }}" required data-option="{{$keys}}">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('temple_category_name') }} </label>
                                <select name="category_id" class="js-select2-custom form-control temple_category_data" placeholder="{{ translate('temple_category_name') }}">
                                    @if($templecategory)
                                    @foreach ($templecategory as $key => $value)
                                    <option value="{{ $value['id']}}" {{ (( $value['id'] == $temple['category_id'])?"selected":"") }}> {{$value['name']}} </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('country_select') }}</label>
                                <select class="js-select2-custom form-control action-get-request-onchange country_select_geolocation" name="country_id" required data-option="{{ $temple['country_id'] }}">
                                    <option value="{{ old('id') }}" selected disabled>{{ translate('country_select') }}</option>
                                    @foreach ($countryList as $countryItem)
                                    <option value="{{ $countryItem['id'] }}" {{ (($countryItem['id'] == $temple['country_id'])?'selected':'') }}>{{ $countryItem['name'] }}</option>
                                    @endforeach
                                </select>
                                <input type='hidden' class='country_id_short_name' name='country_id_short_name'>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('state_select') }}</label>
                                <select class="js-example-basic-multiple js-states js-example-responsive form-control state_select_geolocation action-get-request-onchange " name="state_id" data-option="{{ $temple['state_id'] }}" data-url-prefix="{{ url('/admin/temple/get-cities?id=') }}" data-element-id="sub-cities-select" data-element-type="select">
                                    <option value="0" selected disabled>---{{ translate('select') }}---</option>
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
                                <select class="js-select2-custom form-control sub-districts-select district_select_geolocation" name="district_id" required data-id="{{ $temple['district_id'] }}">
                                    <option selected disabled>{{ translate('select_District') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('cities_select') }}</label>
                                <select name="city_id" class="js-example-basic-multiple js-cities js-example-responsive form-control City_select_geolocation sub-cities-select" data-id="{{ $temple['city_id'] }}">
                                </select>
                                <input type='hidden' name='city_latitude' class='city_latitude'>
                                <input type='hidden' name='city_longitude' class='city_longitude'>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="title-color" for="{{ $language }}_short_description">{{ translate('short_description') }}
                                ({{ strtoupper($language) }})
                            </label>
                            <textarea {{ $language == 'en' ? 'required' : '' }} name="short_description[]" id="{{ $language }}_short_description" class="form-control ckeditor" placeholder="{{ translate('short_description') }}" required>{{ $translate[$language]['short_description'] ?? $temple['short_description'] }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_expect_details">{{ translate('expect_details') }}
                                    ({{ strtoupper($language) }})
                                </label>
                                <textarea {{ $language == $defaultLanguage ? 'required' : '' }} name="expect_details[]" id="{{ $language }}_expect_details" class="form-control ckeditor" placeholder="Expect Details">{{ $translate[$language]['expect_details'] ?? $temple['expect_details'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="lang[]" value="{{ $language }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group pt-4">
                                <label class="title-color">{{ translate('tips_details') }}
                                    ({{ strtoupper($language) }})</label>
                                <textarea name="tips_details[]" class="ckeditor" id="tips_details{{ $language }}">{!! $translate[$language]['tips_details'] ?? $temple['tips_details'] !!}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group pt-4">
                                <label class="title-color">{{ translate('temple_description') }}
                                    ({{ strtoupper($language) }})</label>
                                <textarea name="details[]" class="ckeditor" id="editor{{ $language }}">{!! $translate[$language]['details'] ?? $temple['details'] !!}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group pt-4">
                                <label class="title-color">{{ translate('more_details') }}
                                    ({{ strtoupper($language) }})</label>
                                <textarea name="more_details[]" class="ckeditor" id="editor{{ $language }}">{!! $translate[$language]['more_details'] ?? $temple['more_details'] !!}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $language }}_temple_services">{{ translate('temple_services') }}
                                ({{ strtoupper($language) }})</label>
                            <textarea class="ckeditor" id="temple_services{{ $language }}" name="temple_services[]" required>{!! $translate[$language]['temple_services'] ?? $temple['temple_services'] !!}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $language }}_temple_aarti">{{ translate('temple_aarti') }}
                                ({{ strtoupper($language) }})</label>
                            <textarea class="ckeditor" id="temple_aarti{{ $language }}" name="temple_aarti[]" required>{!! $translate[$language]['temple_aarti'] ?? $temple['temple_aarti'] !!}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $language }}_tourist_place">{{ translate('tourist_place') }}
                                ({{ strtoupper($language) }})</label>
                            <textarea class="ckeditor" id="tourist_place{{ $language }}" name="tourist_place[]" required>{!! $translate[$language]['tourist_place'] ?? $temple['tourist_place'] !!}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $language }}_temple_local_food">{{ translate('temple_local_food') }}
                                ({{ strtoupper($language) }})</label>
                            <textarea class="ckeditor" id="temple_local_food{{ $language }}" name="temple_local_food[]" required>{!! $translate[$language]['temple_local_food'] ?? $temple['temple_local_food'] !!}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_facilities">{{ translate('facilities') }}
                                    ({{ strtoupper($language) }})</label>
                                <input type="text" {{ $language == 'en' ? 'required' : '' }} name="facilities[]" id="{{ $language }}_facilities" value="{{ $translate[$language]['facilities'] ?? $temple['facilities'] }}" class="form-control" placeholder="{{ translate('facilities') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_tips_restrictions">{{ translate('tips_restrictions') }}
                                    ({{ strtoupper($language) }})</label>
                                <input type="text" {{ $language == 'en' ? 'required' : '' }} name="tips_restrictions[]" id="{{ $language }}_tips_restrictions" value="{{ $translate[$language]['tips_restrictions'] ?? $temple['tips_restrictions'] }}" class="form-control" placeholder="{{ translate('tips_restrictions') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_temple_known">{{ translate('temple_known') }}
                                    ({{ strtoupper($language) }})
                                </label>
                                <input type="text" {{ $language == $defaultLanguage ? 'required' : '' }} name="temple_known[]" value="{{ $translate[$language]['temple_known'] ?? $temple['temple_known'] }}" id="{{ $language }}temple_known" class="form-control" placeholder="{{ translate('temple_known') }}">
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
                            <input type="text" name="opening_time" id="opentime" class="form-control" placeholder="{{ translate('opening_time') }}" value="{{ isset($temple['opening_time']) ? date('H:i', strtotime($temple['opening_time'])) : '' }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('closeing_time') }}</label>
                            <input type="text" name="closeing_time" id="closetime" class="form-control" placeholder="{{ translate('closeing_time') }}" value="{{ isset($temple['closeing_time']) ? date('H:i', strtotime($temple['closeing_time'])) : '' }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('require_time') }}</label>
                            <input type="text" name="require_time" class="form-control" placeholder="{{ translate('require_time') }}" value="{{ $temple['require_time'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('entry_fee') }}</label>
                            <input type="text" name="entry_fee" class="form-control" placeholder="{{ translate('entry_fee') }}" value="{{ $temple['entry_fee'] }}">
                        </div>
                    </div>
                    <div class="col-md-12">
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

        <?php
        $matchingTrust = \App\Models\DonateTrust::all()
            ->first(function ($item) use ($temple) {
                $ids = json_decode($item->trust_temple_id, true);
                return in_array($temple['id'], (array) $ids);
            });

        $donateTrustId = $matchingTrust ? $matchingTrust->id : null;
        ?>
        
        <div class="card mt-3 rest-part">
            <div class="card-header">
                <div class="d-flex gap-2">
                    <i class="tio-money_vs" style="font-size: 32px;">money_vs</i>
                    <h4 class="mt-1">{{ translate('VIP_darshan') }}</h4>
                    @if($donateTrustId)
                    <h4 class="mt-1">&nbsp;&nbsp;Trust Name:&nbsp;{{$matchingTrust['trust_name']}}</h4><span style="font-size: 20px;">( <input type="checkbox" name="aadhaar_verify_status" value="1" {{  (old('aadhaar_verify_status',$temple['aadhaar_verify_status']) == 1)?"checked":"" }}> Booking Aadhaar Requirements)</span>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="add-parent" class="btn btn-success mb-3">+ Add Packages</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" id="vip-darshan-wrapper">

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

                                        @if (File::exists(base_path('storage/app/public/temple/logo/' . $temple->logo)))
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-flex">
                                            <i class="tio-delete"></i>
                                        </span>
                                        @else
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        @endif


                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer1" class="h-auto aspect-1 bg-white onerror-add-class-d-none" alt="" src="{{ getValidImage(path: 'storage/app/public/temple/logo/' . $temple->logo, type: 'backend-product') }}">
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

                                        @if (File::exists(base_path('storage/app/public/temple/thumbnail/' . $temple->thumbnail)))
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-flex">
                                            <i class="tio-delete"></i>
                                        </span>
                                        @else
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        @endif


                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer" class="h-auto aspect-1 bg-white onerror-add-class-d-none" alt="" src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . $temple->thumbnail, type: 'backend-product') }}">
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
                                        <input type="text" name="meta_title" value="{{ $temple['meta_title'] }}" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="title-color">
                                            {{ translate('meta_Description') }}
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('write_a_short_description_of_the_InHouse_shops_product') . ' ' . translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
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
                                                    <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                    </span>
                                                </div>

                                            </div>

                                            <div>
                                                <div class="custom_upload_input">
                                                    <input type="file" name="meta_image" class="custom-upload-input-file meta-img action-upload-color-image" id="" data-imgpreview="pre_meta_image_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                                    @if (File::exists(base_path('storage/app/public/temple/meta/' . $temple['meta_image'])))
                                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-flex">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    @else
                                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    @endif

                                                    <div class="img_area_with_preview position-absolute z-index-2 d-flex">
                                                        <img id="pre_meta_image_viewer" class="h-auto aspect-1 bg-white onerror-add-class-d-none" alt="" src="{{ getValidImage(path: 'storage/app/public/temple/meta/' . $temple['meta_image'], type: 'backend-banner') }}">
                                                    </div>
                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                            <img alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
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

<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-want-to-add-or-update-this-service" data-text="{{ translate('want_to_update_this_service') }}"></span>
<span id="message-please-only-input-png-or-jpg" data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
<span id="message-service-added-successfully" data-text="{{ translate('service_added_successfully') }}"></span>
<span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
<span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
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

    $('#timepicker').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    initSample();
</script>
<script>
    "use strict";
    var imageCount = 0;
    var colors = 0;

    let thumbnail =
        "{{ productImagePath('thumbnail') . '/' . $temple->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}";
    $(function() {
        if (imageCount > 0) {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: colors === 0 ? 15 : imageCount,
                rowHeight: 'auto',
                groupClassName: 'col-6 col-md-4 col-xl-3 col-xxl-2',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}",
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
                image: "{{ productImagePath('thumbnail') . '/' . $temple->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}",
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
            let country = $(".country_select_geolocation").data('option');
            $(".country_select_geolocation").val(country).trigger('change.select2');

            let states = $(".state_select_geolocation").data('option');
            $(".state_select_geolocation").val(states).trigger('change.select2');
            let cities = $(".sub-cities-select").attr("data-id");
            getRequestFunctionality1(states, cities);
            getRequestFunctionality('{{ route("admin.temple.get-cities") }}?id=' + states + '&cities=' + cities, 'sub-cities-select', 'select');
        }, 100)
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


            // $('.state_select_geolocation').val(
            //     $('.state_select_geolocation option').filter(function() {
            //         return ($(this).text()).toLowerCase() === state.toLowerCase();
            //     }).val()
            // ).trigger('change.select2');

            getRequestFunctionality1($('.state_select_geolocation').val(), city);
        });

        $(".getAddress_google").on('input', function() {
            var check = $(this).attr('data-option');
            if (check == 0) {
                if ($(this).val().length < 2) {
                    $('input[type="hidden"][name="latitude"]').val('');
                    $('input[type="hidden"][name="longitude"]').val('');
                    $('.country_select_geolocation').val('').trigger('change.select2');
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
                $('.sub-districts-select').empty().append(data.district_tag);
                if (cities_option != null) {
                    $('.City_select_geolocation').val(
                        $('.City_select_geolocation option').filter(function() {
                            return ($(this).text()).toLowerCase() === cities_option.toLowerCase();
                        }).val()
                    ).trigger('change.select2');
                }
                if (!isNaN(parseFloat(cities_option)) && isFinite(cities_option)) {
                    $('.City_select_geolocation').val(cities_option).trigger('change.select2');
                }
            },
            complete: function() {
                $('#loading').fadeOut();
            }
        })
    }
</script>
<script>
    let parentIndex = 0;
    $('#add-parent').click(function() {
        $('#vip-darshan-wrapper').append(`
            <div class="parent-vip-darshan border p-3 mb-3 bg-light rounded">
                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Darshan Name</label>
                    <div class="col-sm-7">
                        <input type="text" name="vipdarshan[${parentIndex}][name]" class="form-control parent-input" placeholder="Enter Darshan Name">
                        <textarea rows="4" name="vipdarshan[${parentIndex}][description]" class="form-control mt-2"  placeholder="Enter Darshan Description"></textarea>
                    </div>
                    <div class="col-sm-3 d-flex">
                        <button type="button" class="btn btn-primary mr-2 add-child">+ Add Darshan Type</button>
                        <button type="button" class="btn btn-danger remove-parent">−</button>
                    </div>
                </div>
                <div class="child-wrapper pl-3"></div>
            </div>
        `);
        parentIndex++;
    });

    // // Remove Parent
    $(document).on('click', '.remove-parent', function() {
        $(this).closest('.parent-vip-darshan').remove();
    });

    // Add Child
    $(document).on('click', '.add-child', function() {
        const parent = $(this).closest('.parent-vip-darshan');
        const parentIdx = parent.index();
        const childWrapper = parent.find('.child-wrapper');
        const childCount = childWrapper.children().length;

        childWrapper.append(`
        <div class="child-vip-darshan border p-2 mt-2 bg-white rounded">
            <div class="form-group row align-items-center">
                <label class="col-sm-2 col-form-label">Package Name</label>
                <div class="col-sm-2">
                    <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][name]" class="form-control child-input" placeholder="Enter Package Name">
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][price]" class="form-control child-input" placeholder="Enter Price"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-sm-4 mt-1">
                            <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][limit]" class="form-control child-input" placeholder="Enter limit" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-sm-4 mt-1">
                            <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][today_price]" class="form-control child-input" placeholder="Enter quick darshan price" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                         <div class="col-sm-4 mt-1">
                            <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][receipt_price]" class="form-control child-input" placeholder="Enter Receipt Price"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-sm-4 mt-1">
                            <input type="text" name="vipdarshan[${parentIdx}][children][${childCount}][platform_fee]" class="form-control child-input" placeholder="Enter Platform Free" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-sm-4 mt-1">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 d-flex p-0">
                    <button type="button" class="btn btn-info btn-sm mr-2 add-include-plan text-white p-1">+ Add Include</button>
                    <button type="button" class="btn btn-warning btn-sm mr-2 add-sub-child text-white p-1">+ Time Slot</button>
                    <button type="button" class="btn btn-danger remove-child">−</button>
                </div>
            </div>
            <div class="sub-include-plan-wrapper pl-4"></div>
            <div class="sub-child-wrapper pl-4"></div>
        </div>
    `);
    });

    // // Remove Child
    $(document).on('click', '.remove-child', function() {
        $(this).closest('.child-vip-darshan').remove();
    });

    //////
    // // Add Sub-Child
    $(document).on('click', '.add-include-plan', function() {
        const child = $(this).closest('.child-vip-darshan');
        const childWrapper = child.find('.sub-include-plan-wrapper');
        const childIdx = child.index();
        const parentIdx = child.closest('.parent-vip-darshan').index();
        const subChildCount = childWrapper.children().length;

        childWrapper.append(`
            <div class="sub-child-include-vip-darshan form-group row align-items-center mt-2">
                <label class="col-sm-4 col-form-label"></label>
                <div class="col-sm-6">
                        <input type="text" class="form-control" name='vipdarshan[${parentIdx}][children][${childIdx}][include][${subChildCount}][name]' value='' placeholder="Enter Included Information">
                </div>                       
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger remove-sub-include-child">−</button>
                </div>
            </div>
        `);
        selectstarttime();
    });

    // // Remove Sub-Child
    $(document).on('click', '.remove-sub-include-child', function() {
        $(this).closest('.sub-child-include-vip-darshan').remove();
    });
    ///////////
    // // Add Sub-Child
    $(document).on('click', '.add-sub-child', function() {
        const child = $(this).closest('.child-vip-darshan');
        const childWrapper = child.find('.sub-child-wrapper');
        const childIdx = child.index();
        const parentIdx = child.closest('.parent-vip-darshan').index();
        const subChildCount = childWrapper.children().length;

        childWrapper.append(`
            <div class="sub-child-vip-darshan form-group row align-items-center mt-2">
                <label class="col-sm-4 col-form-label"></label>
                <div class="col-sm-3">
                        <input type="text" class="form-control start_to_end_time" readonly name='vipdarshan[${parentIdx}][children][${childIdx}][subchildren][${subChildCount}][start_time]' value='' style="border-radius: 5px 0px 0px 5px;" placeholder="{{ translate('start_time') }}">
                </div>
                <div class="col-sm-3">
                        <input type="text" class="form-control start_to_end_time" readonly name='vipdarshan[${parentIdx}][children][${childIdx}][subchildren][${subChildCount}][end_time]' value='' style="border-radius: 0px 5px 5px 0px;" placeholder="{{ translate('end_time') }}">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger remove-sub-child">−</button>
                </div>
            </div>
        `);
        selectstarttime();

    });

    // // Remove Sub-Child
    $(document).on('click', '.remove-sub-child', function() {
        $(this).closest('.sub-child-vip-darshan').remove();
    });


    function selectstarttime() {
        $('.start_to_end_time').each(function() {
            if (!$(this).hasClass('timepicker-initialized')) {
                $(this).timepicker({
                    uiLibrary: 'bootstrap4',
                    timeFormat: 'h:i A',
                    interval: 30,
                    dropdown: true,
                    scrollbar: true
                });
                $(this).addClass('timepicker-initialized');
            }
        });
    }
</script>
<!--  -->
<script>
    let preloadedPermissions = @json(json_decode($temple['vip_plans'] ?? "[]", true));
    console.log(preloadedPermissions);
    $(document).ready(function() {
        if (preloadedPermissions && preloadedPermissions.length > 0) {
            preloadedPermissions.forEach((parent, parentIdx) => {
                $('#vip-darshan-wrapper').append(`
                <div class="parent-vip-darshan border p-3 mb-3 bg-light rounded">
                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Darshan Name</label>
                        <div class="col-sm-7">
                            <input type="text" name="vipdarshan[${parentIdx}][name]" class="form-control parent-input" value="${parent.name}" placeholder="Enter Darshan Name">
                            <textarea rows="4" name="vipdarshan[${parentIdx}][description]" class="form-control mt-2"  placeholder="Enter Darshan Description">${parent.description??''}</textarea>
                        </div>
                        <div class="col-sm-3 d-flex">
                            <button type="button" class="btn btn-primary mr-2 add-child">+ Add Darshan Type</button>
                        </div>
                    </div>
                    <div class="child-wrapper pl-3"></div>
                </div>
            `);

                const parentDiv = $('#vip-darshan-wrapper .parent-vip-darshan').last();

                parent.package.forEach((child, childIdx) => {
                    const childWrapper = parentDiv.find('.child-wrapper');

                    childWrapper.append(`
                    <div class="child-vip-darshan border p-2 mb-2 bg-white rounded">
                        <div class="form-group row align-items-center">
                            <label class="col-sm-2 col-form-label">Package Name</label>
                            <div class="col-sm-2">
                                <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][name]" class="form-control child-input" value="${child.name}" placeholder="Enter Package Name">
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][price]" class="form-control child-input" value="${child.price}" placeholder="Enter Price"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][limit]" class="form-control child-input" value="${child.limit??0}" placeholder="Enter limit" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][today_price]" class="form-control child-input" value="${child.today_price??0}" placeholder="Enter quick darshan price" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>   
                                     <div class="col-sm-4 mt-2">
                                        <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][receipt_price]" class="form-control child-input" value="${child.receipt_price??0}" placeholder="Enter Receipt Price"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                        <input type="text" name="vipdarshan[${parentIdx}][children][${childIdx}][platform_fee]" class="form-control child-input" value="${child.platform_fee??0}" placeholder="Enter Platform Free" min="0"  onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-sm-4 mt-2">
                                    </div>                  
                                </div>
                            </div>
                            <div class="col-sm-2 d-flex p-0">
                                <button type="button" class="btn btn-info btn-sm mr-2 add-include-plan text-white p-1">+ Add Include</button>
                                <button type="button" class="btn btn-warning btn-sm mr-2 add-sub-child text-white p-1">+ Time Slot</button>
                                <button type="button" class="btn btn-danger remove-child">−</button>
                            </div>
                        </div>
                        <div class="sub-include-plan-wrapper pl-4"></div>
                        <div class="sub-child-wrapper pl-4"></div>
                    </div>
                `);

                    const subChildIncludeWrapper = childWrapper.find('.child-vip-darshan').last().find('.sub-include-plan-wrapper');
                    const subChildWrapper = childWrapper.find('.child-vip-darshan').last().find('.sub-child-wrapper');
                    child.include.forEach((subChild, subIdx) => {
                        subChildIncludeWrapper.append(`
                        <div class="sub-child-include-vip-darshan form-group row align-items-center mt-2">
                            <label class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                    <input type="text" class="form-control" name='vipdarshan[${parentIdx}][children][${childIdx}][include][${subIdx}][name]' value='${subChild.name}' placeholder="Enter Included Information">
                            </div>                       
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger remove-sub-include-child">−</button>
                            </div>
                        </div>
                    `);
                    });
                    child.date.forEach((subChild, subIdx) => {
                        subChildWrapper.append(`
                        <div class="sub-child-vip-darshan form-group row align-items-center mb-2">
                            <label class="col-sm-4 col-form-label"></label>                            
                            <div class="col-sm-3">
                                    <input type="text" class="form-control start_to_end_time" readonly name='vipdarshan[${parentIdx}][children][${childIdx}][subchildren][${subIdx}][start_time]' value='${convertTo12HourFormat(subChild.time.split(' - ')[0])}' style="border-radius: 5px 0px 0px 5px;" placeholder="{{ translate('start_time') }}">
                            </div>
                            <div class="col-sm-3">
                                    <input type="text" class="form-control start_to_end_time" readonly name='vipdarshan[${parentIdx}][children][${childIdx}][subchildren][${subIdx}][end_time]' value='${convertTo12HourFormat(subChild.time.split(' - ')[1])}' style="border-radius: 0px 5px 5px 0px;" placeholder="{{ translate('end_time') }}">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger remove-sub-child">−</button>
                            </div>
                        </div>
                    `);
                    });
                    selectstarttime();
                });
            });
            parentIndex = preloadedPermissions.length;
        }
    });

    function convertTo12HourFormat(time12h) {
        const [time, modifier] = time12h.trim().split(' ');
        let [hours, minutes] = time.split(':').map(Number);
        if (modifier === 'PM' && hours !== 12) hours += 12;
        if (modifier === 'AM' && hours === 12) hours = 0;
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }
</script>

@endpush