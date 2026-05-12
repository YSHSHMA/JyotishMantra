@extends('layouts.back-end.app')

@section('title', translate('Edit_Event'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>

@endpush

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Edit_Event') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new add_Organizer -->
        <div class="col-md-12 mb-3">
            <form action="{{ route('admin.event-managment.event.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-body">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <div class="col-md-12">
                                @foreach($language as $lang)
                                <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'event_name') {
                                            $translate[$lang]['event_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'event_about') {
                                            $translate[$lang]['event_about'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'event_schedule') {
                                            $translate[$lang]['event_schedule'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'event_attend') {
                                            $translate[$lang]['event_attend'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'event_team_condition') {
                                            $translate[$lang]['event_team_condition'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'language') {
                                            $translate[$lang]['language'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('Event_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="event_name[]" class="form-control" value="{{ old('event_name.'.$loop->index, $lang == $defaultLanguage ? $getData['event_name'] : ($translate[$lang]['event_name'] ?? '')) }}" placeholder="{{ translate('enter_Event_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="category">{{ translate('Event_category') }}</label>
                                            <select class="form-control all_select_data" data-point='1' name='category_id'>
                                                <option value="" selected disabled>{{ translate('Select_Event_category') }}</option>
                                                @if($category_list)
                                                @foreach($category_list as $val)
                                                <option value="{{ $val['id']}}" {{ (($val['id'] == old('category_id',$getData['category_id']) )?"selected":"") }}>{{ $val['category_name']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="Organized_by">{{ translate('Organized_by') }}</label>
                                            <select class="form-control all_select_data" data-point='2' name='organizer_by'>
                                                <option value="">{{ translate('Select_Organized_by') }}</option>
                                                <option value="inhouse" {{ (('inhouse' == old('organizer_by',$getData['organizer_by']) )?"selected":"") }}>{{ translate('inhouse') }}</option>
                                                <option value="outside" {{ (('outside' == old('organizer_by',$getData['organizer_by']) )?"selected":"") }}>{{ translate('outside') }}</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs w-fit-content mb-4">
                                                <li class="nav-item text-capitalize">
                                                    <a class="nav-link cursor-pointer active" data-toggle="tab" href="#aboutEvent{{$lang}}">
                                                        Add About ({{ strtoupper($lang) }})
                                                    </a>
                                                </li>
                                                <li class="nav-item text-capitalize">
                                                    <a class="nav-link cursor-pointer" data-toggle="tab" href="#eventSchedule{{$lang}}">
                                                        Event Schedule ({{ strtoupper($lang) }})
                                                    </a>
                                                </li>
                                                <li class="nav-item text-capitalize">
                                                    <a class="nav-link cursor-pointer" data-toggle="tab" href="#whyshouldyouattend{{$lang}}">
                                                        why Should You Attend ({{ strtoupper($lang) }})
                                                    </a>
                                                </li>
                                                <li class="nav-item text-capitalize">
                                                    <a class="nav-link cursor-pointer" data-toggle="tab" href="#termscondition{{$lang}}">
                                                        Terms & Conditions ({{ strtoupper($lang) }})
                                                    </a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="aboutEvent{{$lang}}">
                                                    <label class="title-color" for="event_about">{{ translate('event_about') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <textarea name='event_about[]' class='form-control ckeditor'>{{ old('event_about.'.$loop->index,$lang == $defaultLanguage ? $getData['event_about'] : $translate[$lang]['event_about'] ?? '') }} </textarea>
                                                </div>
                                                <div class="tab-pane fade" id="eventSchedule{{$lang}}">
                                                    <label class="title-color" for="event_schedule">{{ translate('event_schedule') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <textarea name='event_schedule[]' class='form-control ckeditor'>{{ old('event_schedule.'.$loop->index,$lang == $defaultLanguage ? $getData['event_schedule'] : $translate[$lang]['event_schedule'] ?? '') }} </textarea>
                                                </div>
                                                <div class="tab-pane fade" id="whyshouldyouattend{{$lang}}">
                                                    <label class="title-color" for="why_should_you_attend">{{ translate('why_should_you_attend') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <textarea name='event_attend[]' class='form-control ckeditor'>{{ old('event_attend.'.$loop->index,$lang == $defaultLanguage ? $getData['event_attend'] : $translate[$lang]['event_attend'] ?? '') }}</textarea>
                                                </div>
                                                <div class="tab-pane fade" id="termscondition{{$lang}}">
                                                    <label class="title-color" for="termconditions">{{ translate('terms_&_Conditions') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <textarea name='event_team_condition[]' class='form-control ckeditor'>{{ old('event_team_condition.'.$loop->index,$lang == $defaultLanguage ? $getData['event_team_condition'] : $translate[$lang]['event_team_condition'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="organizer">{{ translate('event_Organizer') }}</label>
                                            <select class="form-control all_select_data" data-point='3' name='event_organizer_id'>
                                                <option value=''>{{ translate('Select_event_Organizer') }}</option>
                                                @if($organizer_list)
                                                @foreach($organizer_list as $orgval)
                                                <option value="{{ $orgval['id']}}" {{ (($orgval['id'] == old('event_organizer_id',$getData['event_organizer_id']) )?"selected":"") }}>{{ $orgval['organizer_name']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="Age_group">{{ translate('Age_group') }}</label>
                                            <input type="text" class="form-control  all_select_data" data-point='4' name='age_group' value="{{ old('age_group',($getData['age_group']??''))}}" placeholder="{{ translate('Age_group') }}">
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="Event_artist">{{ translate('Event_artist') }}</label>
                                            <select class="form-control all_select_data" data-point='5' name='event_artist' placeholder="{{ translate('Event_artist') }}">
                                                <option value="">Select Artist</option>
                                                @if($artist_list)
                                                @foreach($artist_list as $ar_val)
                                                <option value="{{ $ar_val['id']}}" {{ ((old('event_artist',$getData['event_artist']) == $ar_val['id'] )?'selected':'' )}}>{{ ucwords($ar_val['name'])}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-12 card-head shadow bg-white rounded">
                                            <label class='form-label h3' style="padding: 17px 0px 8px 27px;"><i class='tio-user'></i> General SetUp</label>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class='col-md-4 form-group p-0 mt-2'>
                                                    <label class="title-color font-weight-bold h3" for="Language">{{ translate('Event_informational_status') }}</label>
                                                    <select name="informational_status" onchange="((this.value == 1)?$('.use_interested_and_not').addClass('d-none'):$('.use_interested_and_not').removeClass('d-none'))" class="form-control all_select_data" data-point='6'>
                                                        <option value="0" {{ old('informational_status',$getData['informational_status'] ) == "0" ? 'selected' : '' }}>Not informational</option>
                                                        <option value="1" {{ old('informational_status',$getData['informational_status'] ) == "1" ? 'selected' : '' }}>informational</option>
                                                    </select>
                                                </div>
                                                <div class='col-md-4 form-group p-0 mt-2'>
                                                    <label class="title-color font-weight-bold h3 mt-3">Event Required Aadhar-card</label>
                                                    <input type="checkbox" class="required_aadhar_status_checked" {{ old('required_aadhar_status', $getData['required_aadhar_status'] ?? 0) == 1 ? 'checked' : '' }}>
                                                    <input type="hidden" class="required_aadhar_status_value" name="required_aadhar_status" value="{{ old('required_aadhar_status', $getData['required_aadhar_status'] ?? 0) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="days">{{ translate('Days') }}</label>
                                            <input type="number" class="form-control all_select_data days_select" data-point='7' id='days' name='days' value="{{ old('days',($getData['days']??''))}}" placeholder="{{ translate('enter_total_days_number') }}" onkeyup="create_a_venus(this)">
                                        </div>

                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="start_date_end_date">{{ translate('start_date') }}</label>
                                            <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date',($getData['start_to_end_date']??''))}}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="Language">{{ translate('Language') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" class="form-control" name='language[]' value="{{ old('language.'.$loop->index,($getData['language']??''))}}" placeholder="{{ translate('Language') }}">
                                        </div>
                                    </div>
                                    <div class="row add_new_data_venues_{{$lang}}" data-lang='{{$lang}}'>

                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('event_thumbnail') }}</label>
                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="custom_upload_input">
                                            <input type="file" name="event_image" class="custom-upload-input-file action-upload-color-image image-preview-before-upload" id="" data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white {{ (($getData['event_image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['event_image'], type: 'backend-product')  }}" alt="">
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
                    <div class="col-md-8">

                        <div class="card h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_additional_Image') }}</label>
                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge">{{ translate('Upload_additional_service_Image') }}</span>
                                    <div class='row'>
                                        <div class='col-md-6'>
                                            <div>
                                                <div class="custom_upload_input">
                                                    <input type="file" name="images[]" class="custom-upload-input-file action-upload-color-image image-preview-before-upload_multiple" id="image-input" data-imgpreview="pre_img_viewer_multi" multiple accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                    </div>
                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class='row' id="image-preview-container">
                                                @if(!empty($getData['images']) && json_decode($getData['images']))
                                                @foreach(json_decode($getData['images']) as $list_img)
                                                <div class="img-previews col-6" data-url="{{ $list_img }}" style="border: 1px solid rgb(204, 204, 204); padding: 8px; display: inline-block; position: relative;">
                                                    <img src="{{ getValidImage(path: 'storage/app/public/event/events/'.$list_img, type: 'backend-product') }}" class="h-auto aspect-1 bg-white" style="max-width: 100px; margin: 10px;">
                                                    <button type="button" class="btn btn-danger btn-sm remove-image" data-url="{{ $list_img }}" style="display: block; margin: 10px auto;">
                                                        <i class="tio-delete"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                                @endif
                                                <input type="hidden" name="old_image" id="old_images" value="{{ !empty($getData['images']) ? $getData['images'] : json_encode([]) }}">
                                            </div>
                                            <div class='row' id='pre_img_viewer_multi'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <i class="tio-user-big"></i>
                                        <h4 class="mb-0">{{ translate('Service_video') }}</h4>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}.">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="title-color mb-0">{{ translate('youtube_video_link') }}</label>
                                        <span class="text-info">({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
                                    </div>
                                    <input type="text" name="youtube_video" value="{{ ($getData['youtube_video']??'')}}" placeholder="{{ translate('ex') . ': https://www.youtube.com/embed/5R06LRdUCSE' }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-1 rest-part">
                    <div class="card-header">
                        <div class="d-flex gap-2">
                            <i class="tio-user-big"></i>
                            <h4 class="mb-0">
                                {{ translate('seo_section') }}
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('add_meta_titles_descriptions_and_images_for_event') . ', ' . translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
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
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('add_the_event_title_name_taglines_etc_here') . ' ' . translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_event_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </label>
                                    <input type="text" name="meta_title" value="{{ old('meta_title',($getData['meta_title']??'')) }}" placeholder="{{ translate('meta_Title') }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="title-color">
                                        {{ translate('meta_Description') }}
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('write_a_short_description_of_the_InHouse_shops_event') . ' ' . translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_event_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </label>
                                    <textarea rows="4" type="text" name="meta_description" class="form-control">{{ old('meta_description',($getData['meta_description']??'')) }}</textarea>
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
                                                <input type="file" name="meta_image" class="custom-upload-input-file meta-img action-upload-color-image image-preview-before-upload" id="" data-imgpreview="pre_meta_image_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                    <i class="tio-delete"></i>
                                                </span>

                                                <div class="img_area_with_preview position-absolute z-index-2">
                                                    <img id="pre_meta_image_viewer" class="h-auto bg-white onerror-add-class-d-none {{ (($getData['meta_image'])?'':'d-none') }}" alt="" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['meta_image'], type: 'backend-product')  }}">
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


                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="packageModal" tabindex="-1" role="dialog" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="packageModalLabel">{{ translate('Package List') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-packages-reset">
                <div class="modal-body">
                    <div class='col-md-12 form-group'>
                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('Package') }}</label>
                    </div>
                    <div class='col-md-12 form-group add_new_packages'>
                        <div class="row">
                            <div class='col-4'>
                                <label class="title-color fw-bolder" for="Language">{{ translate('package_name') }}</label>
                            </div>
                            <div class='col-4'>
                                <label class="title-color fw-bolder" for="Language">{{ translate('No_of_seats') }}</label>
                            </div>
                            <div class="col-4">
                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                            </div>
                        </div>
                        <div class="row mt-2 package-row">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="save-package">{{ translate('Save changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    function selectstarttime(that) {
        var startTime = $(that).val();
        var point = $(that).data('point');
        $(`.all_select_datas[data-point="${point}"]`).val(startTime);
        $(`.start_to_end_time2[data-point="${point}"]`).timepicker('option', 'minTime', startTime);
        create_timesIN(point);
    }

    // $('.start_to_end_time2').on('changeTime', function() {
    // });
    function selectendtime(that) {
        var endTime = $(that).val();
        var point = $(that).data('point');
        $(`.all_select_datae[data-point="${point}"]`).val(endTime);
        $(`.start_to_end_time1[data-point="${point}"]`).timepicker('option', 'maxTime', endTime);
        create_timesIN(point);
    }


    function create_timesIN(point) {
        var startTime = $(`.start_to_end_time1[data-point="${point}"]`).val();
        var endTime = $(`.start_to_end_time2[data-point="${point}"]`).val();
        if (startTime && endTime) {
            var duration = getTimeDuration(startTime, endTime);
            $(`.duration_output[data-point="${point}"]`).val(duration.hours + ':' + duration.minutes);
        } else {
            $(`.duration_output[data-point="${point}"]`).val('00:00');
        }
    }

    function getTimeDuration(start, end) {
        var startMinutes = convertToMinutes(start);
        var endMinutes = convertToMinutes(end);

        var durationMinutes = endMinutes - startMinutes;
        if (durationMinutes < 0) {
            durationMinutes += 24 * 60; // Handle cases where end time is after midnight
        }

        var hours = Math.floor(durationMinutes / 60);
        var minutes = durationMinutes % 60;

        return {
            hours: hours,
            minutes: minutes
        };
    }

    function convertToMinutes(time) {
        var timeParts = time.match(/(\d+):(\d+)\s?(AM|PM)/);
        var hours = parseInt(timeParts[1]);
        var minutes = parseInt(timeParts[2]);
        var period = timeParts[3];

        if (period === 'PM' && hours !== 12) {
            hours += 12;
        }
        if (period === 'AM' && hours === 12) {
            hours = 0;
        }
        return hours * 60 + minutes;
    }

    function initializeDateRangePicker(isSingleDate) {
        var initialDateRange = "<?= ($getData['start_to_end_date'] ?? '') ?>";
        var startDate, endDate;
        if (initialDateRange) {
            var dates = initialDateRange.split(' - ');
            startDate = moment(dates[0], 'YYYY-MM-DD');
            endDate = moment(dates[1], 'YYYY-MM-DD');
        } else {
            startDate = moment().startOf('day');
            endDate = moment().endOf('day');
        }
        $('.start_date_end_date').daterangepicker({
            singleDatePicker: isSingleDate,
            startDate: startDate,
            endDate: endDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: start.format('YYYY-MM-DD'),
                maxDate: end.format('YYYY-MM-DD')
            });
        });
        if (initialDateRange && initialDateRange.includes(' - ')) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: startDate.format('YYYY-MM-DD'),
                maxDate: endDate.format('YYYY-MM-DD')
            });
        }
    }
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        calculateTotalAmount();
        $('.seats_no, .price_no').on('input', calculateTotalAmount);


        new_dates();



        // Initial setup for date range picker
        initializeDateRangePicker(true);
        day_input();
    });

    function day_input() {
        var value = $(".days_select").val();
        if (!/^\d*$/.test(value)) {
            toastr.error("Please enter a valid number");
            $(".days_select").val('');
        } else if (value) {
            var days = parseInt(value);

            if (days === 1) {
                initializeDateRangePicker(true);
            } else if (days > 1) {
                initializeDateRangePicker(false);

                // var startDate = new Date();
                // var endDate = new Date();
                // endDate.setDate(startDate.getDate());

                var initialDateRange = "<?= ($getData['start_to_end_date'] ?? '') ?>";
                var startDate, endDate;
                if (initialDateRange) {
                    var dates = initialDateRange.split(' - ');
                    startDate = moment(dates[0], 'YYYY-MM-DD');
                    endDate = moment(dates[1], 'YYYY-MM-DD');
                } else {
                    startDate = moment().startOf('day');
                    endDate = moment().endOf('day');
                }
                $('.start_date_end_date').data('daterangepicker').setStartDate(startDate);
                $('.start_date_end_date').data('daterangepicker').setEndDate(endDate);
            }
        }
    }
    // add packeages

    function add_new_html() {
        var newRow = `
                <div class="row mt-2 package-row">
                    <div class='col-4'>
                        <select class="form-control package_name" name="packeage_id[]">
                            <option value="" selected disabled>{{ translate('Select_package_name') }}</option>
                                            @if($package_list)
                                                @foreach($package_list as $packval)
                                                    <option value="{{ $packval['id']}}">{{ $packval['package_name']}}</option>
                                                @endforeach
                                            @endif
                        </select>
                    </div>
                    <div class='col-4'>
                        <input type='text' class="form-control seats_no" name="seats_no[]" placeholder="Enter No Of Seats">
                    </div>
                    <div class="col-3">
                        <input type='text' class="form-control price_no" name="price[]" placeholder="Enter Price">
                    </div>
                    <div class="col-1">
                        <a class='btn btn-danger btn-sm' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                    </div>
                </div>
            `;
        $('.add_new_packages').append(newRow);
        $('.seats_no, .price_no').on('input', calculateTotalAmount);
    }

    function remove_html(that) {
        $(that).closest('.row').remove();
        calculateTotalAmount();
    }

    function calculateTotalAmount() {
        var totalAmount = 0;
        var totalseats = 0;
        $('.add_new_packages .row').each(function() {
            var seats = parseFloat($(this).find('.seats_no').val()) || 0;
            var price = parseFloat($(this).find('.price_no').val()) || 0;
            totalAmount += seats * price;
            totalseats += seats;
        });
        $('#show_total_amount').text(`₹ ${totalAmount.toFixed(2)}`);
        $('#show_total_seats').text(`${totalseats} Seats`);
    }

    function key_pass_work(that) {
        // var value = $(that).val();
        // $('.key_pass_work').each(function() {
        //     if ($(this).data('point') !== 0 && $(this).val() === '') {
        //         $(this).val(value);
        //     }
        // });
    }

    $(".all_select_data").on('change', function() {
        var point = $(this).data('point');
        $(`.all_select_data[data-point="${point}"]`).val($(this).val());
    });

    function selectdate(that) {
        var point = $(that).data('point');
        $(`.all_select_data_date[data-point="${point}"]`).val($(that).val());
    }
    $('.image-preview-before-upload').on('change', function() {
        let getElementId = $(this).data('imgpreview');
        $(`#${getElementId}`).removeClass('d-none');
        $(`#${getElementId}`).attr('src', window.URL.createObjectURL(this.files[0]))
    })

    $(document).ready(function() {
        $('.image-preview-before-upload_multiple').on('change', function() {
            let previewContainerId = $(this).data('imgpreview');
            let previewContainer = $(`#${previewContainerId}`);
            previewContainer.empty();

            Array.from(this.files).forEach((file, index) => {
                let reader = new FileReader();

                reader.onload = function(e) {
                    let img = $('<img>').attr('src', e.target.result).addClass('h-auto aspect-1 bg-white').css({
                        'max-width': '100px',
                        'margin': '10px'
                    });

                    let removeButton = $('<button>').html('<i class="tio-delete"></i>').addClass('btn btn-danger btn-sm').css({
                        'display': 'block',
                        'margin': '10px auto'
                    });

                    removeButton.on('click', function() {
                        $(this).parent().remove();
                        removeFile(index);
                    });

                    let imgDiv = $('<div>').addClass('img-preview col-6').css({
                        'border': '1px solid #ccc',
                        'padding': '8px',
                        'display': 'inline-block',
                        'position': 'relative'
                    }).append(img).append(removeButton);

                    previewContainer.append(imgDiv);
                };

                reader.readAsDataURL(file);
            });

            previewContainer.removeClass('d-none');
        });

        function removeFile(index) {
            let input = $('#image-input')[0];
            let dt = new DataTransfer();

            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });

            input.files = dt.files;
        }
    });

    function eventDeleteData(name) {
        var days = $(".days_select").val();
        $(".days_select").val((Number(days) - 1));
        $(`.${name}`).remove();
    }

    function new_dates() {
        $('.datePickers').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }

    timesetAll();

    function timesetAll() {
        $('.start_to_end_time1').timepicker({
            timeFormat: 'h:i A',
            interval: 10,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
        });
        $('.start_to_end_time2').timepicker({
            timeFormat: 'h:i A',
            interval: 10,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const imageContainer = document.getElementById('image-preview-container');
        const oldImagesInput = document.getElementById('old_images');
        let images = JSON.parse(oldImagesInput.value);

        imageContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-image') || event.target.closest('.remove-image')) {
                const button = event.target.closest('.remove-image');
                const imageUrl = button.getAttribute('data-url');
                const imagePreview = document.querySelector(`.img-previews[data-url='${imageUrl}']`);
                if (imagePreview) {
                    imageContainer.removeChild(imagePreview);
                }
                images = images.filter(image => image !== imageUrl);
                oldImagesInput.value = JSON.stringify(images);
            }
        });
    });

    //
    $(".getAddress_google").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment']
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            var points = $(inputElement).data('point');

            $(`.lat_event[data-point="${points}"]`).val(lat);
            $(`.log_event[data-point="${points}"]`).val(lng);


            let zipcode = '',
                city = '',
                state = '',
                country = '';
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

            var points = $(inputElement).data('point');
            $(`.country_event[data-point="${points}"]`).val(country);
            $(`.state_event[data-point="${points}"]`).val(state);
            $(`.cities_event[data-point="${points}"]`).val(city);

        });
    });
</script>

<script>
    window.onload = function() {
        var inputElement = document.querySelector('.days_select');
        if (inputElement) {
            create_a_venus(inputElement);
        }
    };

    function create_a_venus(val) {
        day_input();
        var oldData = {!!json_encode(old() ? : new \stdClass) !!};
        if ($(val).val() <= 20) {
            var key = $(val).val();
            var language = JSON.parse('{!! json_encode($language) !!}');
            var all_venue_data = JSON.parse('{!! json_encode(json_decode($getData["all_venue_data"],true)) !!}'); // Properly parse all_venue_data
            console.log(all_venue_data);
            language.forEach(function(item, index) {
                var item1 = item === 'in' ? 'hi' : item;
                var html = ``;
                for (let index = 0; index < key; index++) {
                    var oldVenue = oldData?.venue?.[index]?.[`${item1}_event_venue`] ?? all_venue_data?.[index]?.[`${item1}_event_venue`] ?? '';
                    var oldVenue_place = oldData?.venue?.[index]?.[`${item1}_event_venue_full_address`] ?? all_venue_data?.[index]?.[`${item1}_event_venue_full_address`] ?? '';
                    var oldCountry = oldData?.venue?.[index]?.[`${item1}_event_country`] ?? all_venue_data?.[index]?.[`${item1}_event_country`] ?? '';
                    var oldState = oldData?.venue?.[index]?.[`${item1}_event_state`] ?? all_venue_data?.[index]?.[`${item1}_event_state`] ?? '';
                    var oldCities = oldData?.venue?.[index]?.[`${item1}_event_cities`] ?? all_venue_data?.[index]?.[`${item1}_event_cities`] ?? '';
                    var oldlat = oldData?.venue?.[index]?.[`${item1}_event_lat`] ?? all_venue_data?.[index]?.[`${item1}_event_lat`] ?? '';
                    var oldlong = oldData?.venue?.[index]?.[`${item1}_event_long`] ?? all_venue_data?.[index]?.[`${item1}_event_long`] ?? '';
                    var oldDate = oldData?.venue?.[index]?.[`date`] ?? all_venue_data?.[index]?.[`date`] ?? '';
                    var oldStartTime = oldData?.venue?.[index]?.[`start_time`] ?? all_venue_data?.[index]?.[`start_time`] ?? '';
                    var oldEndTime = oldData?.venue?.[index]?.[`end_time`] ?? all_venue_data?.[index]?.[`end_time`] ?? '';
                    var oldEventDuration = oldData?.venue?.[index]?.[`event_duration`] ?? all_venue_data?.[index]?.[`event_duration`] ?? '';
                    // var oldCountry = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`${item1}_event_country`] || '';
                    // var oldState = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`${item1}_event_state`] || '';
                    // var oldCities = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`${item1}_event_cities`] || '';
                    // var oldDate = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`date`] || '';
                    // var oldStartTime = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`start_time`] || '';
                    // var oldEndTime = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`end_time`] || '';
                    // var oldEventDuration = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index][`event_duration`] || '';

                    var oldPackageList = oldData?.venue?.[index]?.[`package_list`] ??
                        ((all_venue_data?.[index]?.[`package_list`] && all_venue_data[index][`package_list`] !== '') ?
                            JSON.stringify(all_venue_data[index][`package_list`]) :
                            '');

                    // var oldPackageList = oldData?.venue?.[index]?.[`package_list`] ?? (((all_venue_data[index][`package_list`]??"") !='')? JSON.stringify(all_venue_data[index][`package_list`]):"") ?? '';
                    // var oldPackageList = oldData && oldData['venue'] && oldData['venue'][index] && oldData['venue'][index]['package_list'] || '';


                    html += `<div class='col-md-3 form-group'>
                                <label class="title-color" for="event_venue">{{ translate('Event_Venue') }}</label>
                                <input type="text" class="form-control key_pass_work getAddress_google"  ${ ((index == 0 ) ? "onkeyup='key_pass_work(this)'":"")} data-point="${index}" name='venue[${index}][${item1}_event_venue]' placeholder="{{ translate('enter_Event_name') }}" value='${oldVenue}'>
                                <input type="text" class="form-control key_pass_work_full_address"  ${ ((index == 0 ) ? "onkeyup='key_pass_work12(this)'":"")} data-point="${index}" name='venue[${index}][${item1}_event_venue_full_address]' placeholder="{{ translate('enter_Event_name_full_address') }}" value='${oldVenue_place}' required>
                                <input type='hidden' name="venue[${index}][${item1}_event_country]" data-point="${index}" class='country_event' value='${oldCountry}'>
                                <input type='hidden' name="venue[${index}][${item1}_event_state]" data-point="${index}" class='state_event' value='${oldState}'>
                                <input type='hidden' name="venue[${index}][${item1}_event_cities]" data-point="${index}" class='cities_event' value='${oldCities}'>
                                 <input type='hidden' name="venue[${index}][${item1}_event_lat]" data-point="${index}" class='lat_event' value='${oldlat}'>
                                <input type='hidden' name="venue[${index}][${item1}_event_long]" data-point="${index}" class='long_event' value='${oldlong}'>
                            </div>
                            <div class='col-md-3 form-group'>
                                <label class="title-color" for="dates">{{ translate('date') }}</label>
                                <input type="text" class="form-control datePickers all_select_data_date"  onchange="selectdate(this)" data-point='${index}' name='venue[${index}][date]' value='${oldDate}'>
                            </div>
                            <div class='col-md-3 form-group'>
                                <label class="title-color" for="Event_Time">{{ translate('Event_Time') }}</label>
                                <div class="row">
                                    <input type="text" class="form-control start_to_end_time1 all_select_datas" onchange="selectstarttime(this)" data-point='${index}' name='venue[${index}][start_time]' value='${oldStartTime}' style="width: 97px;border-right: none;border-radius: 5px 0px 0px 5px;" name='time' placeholder="{{ translate('start_time') }}">
                                    <input type="text" class="form-control start_to_end_time2 all_select_datae" onchange="selectendtime(this)" data-point='${index}' name='venue[${index}][end_time]' value='${oldEndTime}' style="width: 97px;border-left: none;border-radius: 0px 5px 5px 0px;" name='time' placeholder="{{ translate('end_time') }}">
                                </div>
                            </div>
                            <div class='col-md-3 form-group'>
                                <input type="hidden" class="form-control duration_output" data-point='${index}' value='${oldEventDuration}' name='venue[${index}][event_duration]'  placeholder="{{ translate('event_duration') }}">`;
                    var inter = $('select[name="informational_status"]').val();
                    html += ` <a onclick='add_packages_seats(${index})' class="btn btn-primary mt-4 use_interested_and_not ${((inter == 1)?"d-none":"")}"><i class="tio-checkmark_circle_outlined ${((oldPackageList == '')?'d-none':'')}  check-package-list-${index}">checkmark_circle_outlined</i> add package</a>
                                <input type="hidden" name="venue[${index}][package_list]" value='${oldPackageList}' class="package_list${index}">
                            </div>`;
                }
                $(`.add_new_data_venues_${item}`).html(html);
            })
            new_dates();
            timesetAll();
            $(".getAddress_google").each(function() {
                let inputElement = this;
                let lang = $(inputElement).closest('.row').data('lang'); // Fetch language from data attribute

                // Initialize the autocomplete with the correct language
                let autocomplete = new google.maps.places.Autocomplete(inputElement, {
                    types: ['establishment'],
                });

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $(inputElement).val('');
                        return;
                    }
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    var points = $(inputElement).data('point');
                    var langu = ((lang == 'in') ? 'hi' : lang);
                    getHindiAddress(lat, lng, langu, points, inputElement);
                });
            });

        } else {
            $(val).val('20');
            toastr.error('It cannot be created 20+');
        }
    }

    function getHindiAddress(lat, lng, langu, points, inputElement) {
        const apiKey = '{{$googleMapsApiKey}}';
        const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&language=${langu}&key=${apiKey}`;

        $.getJSON(geocodeUrl, function(data) {
            if (data.status === 'OK' && data.results.length > 0) {
                let fullAddress = '';
                let city = '';
                let state = '';
                let country = '';
                let streetNumber = '';
                let streetName = '';

                // Log the full API response for debugging
                console.log(data.results);

                data.results[0].address_components.forEach(function(component) {
                    const componentType = component.types[0];
                    switch (componentType) {
                        case 'street_number':
                            streetNumber = component.long_name; // Extract street number
                            break;
                        case 'route':
                            streetName = component.long_name; // Extract street name
                            break;
                        case 'locality':
                        case 'sublocality':
                            city = component.long_name; // Extract city name
                            break;
                        case 'administrative_area_level_1':
                            state = component.long_name; // Extract state name
                            break;
                        case 'country':
                            country = component.long_name; // Extract country name
                            break;
                    }
                });

                // Construct the full address in Hindi
                fullAddress = [streetNumber, streetName, city, state, country].filter(Boolean).join(', ');

                // Set the values in the input fields
                if (points == 0) {
                    $(inputElement).closest('.row').find('.getAddress_google').val(fullAddress);
                    $(inputElement).closest('.row').find('.country_event').val(country);
                    $(inputElement).closest('.row').find('.state_event').val(state);
                    $(inputElement).closest('.row').find('.cities_event').val(city);
                    $(inputElement).closest('.row').find('.lat_event').val(lat);
                    $(inputElement).closest('.row').find('.long_event').val(lng);
                } else {
                    $(inputElement).val(fullAddress);
                    $(inputElement).closest('.row').find(`.country_event[data-point="${points}"]`).val(country);
                    $(inputElement).closest('.row').find(`.state_event[data-point="${points}"]`).val(state);
                    $(inputElement).closest('.row').find(`.cities_event[data-point="${points}"]`).val(city);
                    $(inputElement).closest('.row').find(`.lat_event[data-point="${points}"]`).val(lat);
                    $(inputElement).closest('.row').find(`.long_event[data-point="${points}"]`).val(lng);
                }
            } else {
                console.error('Geocoding API error:', data.status);
            }
        });
    }


    function add_packages_seats(index) {
        $("#add-packages-reset")[0].reset();
        // $('.add_new_packages .package-row:first').remove();

        var savedData = $(`.package_list${index}`).val();

        var newRow = `<div class="row">
                            <div class='col-4'>
                            <label class="title-color fw-bolder" for="Language">{{ translate('package_name') }}</label>
                            </div>
                            <div class='col-4'>
                            <label class="title-color fw-bolder" for="Language">{{ translate('No_of_seats') }}</label>
                            </div>
                            <div class="col-4">
                            <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                            </div>
                            </div>`;
        if (savedData) {
            var data = JSON.parse(savedData);
            data.forEach((entry, idx) => {
                newRow += `
                <div class="row mt-2 package-row">
                    <div class='col-4'>
                        <select class="form-control package_name" name="packeage_id[]" data-option="${entry.package_name}">
                            <option value="" selected disabled>{{ translate('Select_package_name') }}</option>
                                            @if($package_list)
                                                @foreach($package_list as $packval)
                                                    <option value="{{ $packval['id']}}" `;
                if (entry.package_name == "{{ $packval['id']}}") {
                    newRow += 'selected';
                }
                newRow += `>{{ $packval['package_name']}}</option>
                                                @endforeach
                                            @endif
                        </select>
                    </div>
                    <div class='col-4'>
                        <input type='text' class="form-control seats_no" name="seats_no[]" value="${entry.seats_no}" placeholder="Enter No Of Seats">
                    </div>
                    <div class="col-3">
                        <input type='text' class="form-control price_no" name="price[]" value="${entry.price_no}" placeholder="Enter Price">
                    </div>`;
                if (idx == 0) {
                    newRow += `<div class="col-1">
                                <a class='btn btn--primary btn-sm' onclick="add_new_html()"><i class='tio-add'></i></a>
                        </div>`;
                } else {
                    newRow += `<div class="col-1">
                        <a class='btn btn-danger btn-sm' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                    </div>`;
                }
                newRow += `</div>
            `;
            });

            $(".add_new_packages").html(newRow);
        } else {
            newRow += `
                <div class="row mt-2 package-row">
                    <div class='col-4'>
                        <select class="form-control package_name" name="packeage_id[]">
                            <option value="" selected disabled>{{ translate('Select_package_name') }}</option>
                                            @if($package_list)
                                                @foreach($package_list as $packval)
                                                    <option value="{{ $packval['id']}}">{{ $packval['package_name']}}</option>
                                                @endforeach
                                            @endif
                        </select>
                    </div>
                    <div class='col-4'>
                        <input type='text' class="form-control seats_no" name="seats_no[]" placeholder="Enter No Of Seats">
                    </div>
                    <div class="col-3">
                        <input type='text' class="form-control price_no" name="price[]" placeholder="Enter Price">
                    </div>`;
            newRow += `<div class="col-1">
                                <a class='btn btn--primary btn-sm' onclick="add_new_html()"><i class='tio-add'></i></a>
                        </div>`;
            newRow += `</div>
            `;
            $(".add_new_packages").html(newRow);
        }

        $('#packageModal').modal('show');
        $('#save-package').off('click').on('click', function() {
            var formData = [];

            $('.add_new_packages .package-row').each(function() {
                var package_name = $(this).find('.package_name').val();
                var seats_no = $(this).find('.seats_no').val();
                var price_no = $(this).find('.price_no').val();

                if (package_name && seats_no && price_no) {
                    formData.push({
                        package_name: package_name,
                        seats_no: seats_no,
                        price_no: price_no
                    });
                }
            });
            console.log(formData);
            if ((formData && Object.keys(formData).length > 0)) {
                $(`.package_list${index}`).val(JSON.stringify(formData));
                $(`.check-package-list-${index}`).removeClass('d-none');
            } else {
                $(`.package_list${index}`).val('');
                $(`.check-package-list-${index}`).addClass('d-none');
            }

            $('#packageModal').modal('hide');
        });
    }
</script>
<script>
    $('.required_aadhar_status_checked').change(function(){
        $('.required_aadhar_status_value').val(this.checked ? 1 : 0);
    })
</script>
@endpush