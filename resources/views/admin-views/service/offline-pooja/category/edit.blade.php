{{-- @php
    dd($offlinePoojaCategory);
@endphp --}}
@extends('layouts.back-end.app')

@section('title', translate('offline_Pooja_Category_Update'))

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
                {{ translate('offline_Pooja_Category_Update') }}
            </h2>
        </div>

        <div class="col-12">
            <form class="product-form text-start"
                action="{{ route('admin.service.offline.pooja.category.update', $offlinePoojaCategory['id']) }}"
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
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    @foreach ($languages as $language)
                                        <?php
                                        if (count($offlinePoojaCategory['translations'])) {
                                            $translate = [];
                                            foreach ($offlinePoojaCategory['translations'] as $translation) {
                                                if ($translation->locale == $language && $translation->key == 'name') {
                                                    $translate[$language]['name'] = $translation->value;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form"
                                            id="{{ $language }}-form">
                                            <div class="form-group">
                                                <label class="title-color"
                                                    for="{{ $language }}_name">{{ translate('name') }}
                                                    ({{ strtoupper($language) }})
                                                </label>
                                                <input type="text" {{ $language == 'en' ? 'required' : 'required' }}
                                                    class="form-control" name="name[]"
                                                    value="{{ $translate[$language]['name'] ?? $offlinePoojaCategory['name'] }}" />
                                            </div>

                                            <input type="hidden" name="lang[]" value="{{ $language }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="from_part_2">
                                    <label class="title-color">{{ translate('category_Logo') }}</label>
                                    <span class="text-info"><span class="text-danger">*</span>
                                        {{ THEME_RATIO[theme_root_path()]['Category Image'] }}</span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="category-image"
                                            class="custom-file-input image-preview-before-upload" data-preview="#viewer"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                            for="category-image">{{ translate('choose_File') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-5 mt-lg-0 from_part_2">
                                <div class="form-group">
                                    <div class="text-center mx-auto">
                                        <img class="upload-img-view"
                                             id="viewer"
                                             src="{{ getValidImage(path: 'storage/app/public/offlinepooja/category/'. $offlinePoojaCategory['image'] , type: 'backend-basic') }}"
                                             alt=""/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
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
@endpush
