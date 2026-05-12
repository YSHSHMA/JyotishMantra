@extends('layouts.back-end.app-tour')

@section('title', translate('add_visit_place'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('add_visit_place') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new tour_package -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tour-vendor.tour_visits.visit_place_edit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($languages as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <div class="col-md-12">
                                @foreach($languages as $lang)
                                <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'name') {
                                            $translate[$lang]['name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'time') {
                                            $translate[$lang]['time'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('place_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control" placeholder="{{ translate('place_name') }}" value="{{ old('name.'.$loop->index,($translate[$lang]['name']??$getData['name'])) }}" {{$lang == $defaultLanguage? 'required':''}}>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                            <input type="hidden" name='id' value="{{ $getData['id']}}">
                                            <input type="hidden" name='tour_visit_id' value="{{ $getData['tour_visit_id']}}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-color" for="time">{{ translate('time') }}</label>
                                            <input type="text" name="time[]" class="form-control" value="{{ old('time.'.$loop->index,($translate[$lang]['time']??$getData['time'])) }}" placeholder="{{ translate('time') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span>({{ strtoupper($lang) }})</label>
                                            <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}>{{ old('description.'.$loop->index,($translate[$lang]['description']??$getData['description'])) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="additional_image_column col-md-12">
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
                                        <p class="text-muted">{{ translate('upload_additional_images') }}</p>
                                        <div class="coba-area">

                                            <div class="row g-2" id="additional_Image_Section">

                                                @if (!empty($getData['images']) && json_decode($getData['images'],true))
                                                @foreach (json_decode($getData['images'],true) as $key => $photo)
                                                @php($unique_id = rand(1111, 9999))
                                                <div class="col-sm-12 col-md-3">
                                                    <div
                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                            href="{{ route('tour-vendor.tour_visits.visit-delete-image', ['id' => $getData['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>
                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                alt=""
                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $photo, type: 'backend-product') }}">
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
                                                @endif

                                                <div class="col-sm-12 col-md-3">
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
                        <!-- Buttons for form actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush