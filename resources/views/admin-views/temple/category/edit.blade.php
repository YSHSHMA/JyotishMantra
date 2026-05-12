@extends('layouts.back-end.app')

@section('title', translate('Edit_temple_Category'))

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
            {{ translate('Edit_temple_Category') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.temple.category.edit',[$getData['id']]) }}" method="POST" enctype="multipart/form-data" id="services_form">
        @csrf
        <div class="card">
            <div class="px-4 pt-3">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    @foreach ($language as $lang)
                    <li class="nav-item">
                        <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer" id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row ">
                          @foreach ($language as $lang)
                          <?php
                           if (count($getData['translations'])) {
                            $translate = [];
                            foreach ($getData['translations'] as $translation) {
                                if ($translation->locale == $lang && $translation->key == 'name') {
                                    $translate[$lang]['name'] = $translation->value;
                                }
                                if ($translation->locale == $lang && $translation->key == 'short_description') {
                                    $translate[$lang]['short_description'] = $translation->value;
                                }                               
                            }
                        }
                          ?>
                            <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="{{ $lang }}_name">{{ translate('category_name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="name[]" id="{{ $lang }}_name" class="form-control" value="{{ $translate[$lang]['name'] ?? $getData['name'] }}" placeholder="{{ translate('temple_category_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="{{ $lang }}_short_description">{{ translate('short_description') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="short_description[]" id="{{ $lang }}_short_description" class="form-control ckeditor" placeholder="short descscription">{{ $translate[$lang]['short_description'] ?? $getData['short_description'] }}</textarea>

                                    </div>
                                </div>

                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('temple_category_thumbnail') }}</label>
                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="custom_upload_input">
                                            <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                            </div>
                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <img alt="" class="w-75" src="{{ getValidImage(path: 'storage/app/public/temple/category/'.$getData['image'], type: 'backend-product') }}">
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
        </div>



        <div class="mt-3 rest-part">
            <div class="row g-2">




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

@endpush