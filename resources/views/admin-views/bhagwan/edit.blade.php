@extends('layouts.back-end.app')

@section('title', translate('Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/bhagwan.png') }}" alt="">
                {{ translate('Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.bhagwan.update', [$bhagwan['id']]) }}" method="post"
                              enctype="multipart/form-data">
                            @csrf

                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($language as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                           id="{{$lang}}-link">
                                            {{ucfirst(getLanguageName($lang)).'('.strtoupper($lang).')'}}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="week_checkbox" class="title-color">
                                        {{ translate('Week') }}
                                    </label>
                                    <input type="checkbox" id="week_checkbox">
                                    <div class="form-group pb-1">
                                        <select id="week_select" name="week" class="form-control d-none">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                            <option value="Sunday">Sunday</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    @foreach($language as $lang)
                                            <?php
                                            if (count($bhagwan['translations'])) {
                                                $translate = [];
                                                foreach ($bhagwan['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == "name") {
                                                        $translate[$lang]['name'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                                             id="{{$lang}}-form">
                                            <label class="title-color" for="name">{{ translate('Name') }}
                                                ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]"
                                                   value="{{$lang == $defaultLanguage ? $bhagwan['name']:($translate[$lang]['name']??'') }}"
                                                   class="form-control" id="name"
                                                   placeholder="{{ translate('ex') }} : {{ translate('Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
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
                                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('bhagwan_thumbnail') }}</label>
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

                                                        @if (File::exists(base_path('storage/app/public/bhagwan/thumbnail/' . $bhagwan->thumbnail)))
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
                                                                src="{{ getValidImage(path: 'storage/app/public/bhagwan/thumbnail/' . $bhagwan->thumbnail, type: 'backend-product') }}">
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
                                                <p class="text-muted">{{ translate('upload_additional_images') }}</p>
                                                <div class="coba-area">

                                                    <div class="row g-2" id="additional_Image_Section">

                                                        @if (is_array($bhagwan['images']) && count($bhagwan['images']) == 0)
                                                            @foreach (json_decode($bhagwan['images']) as $key => $photo)
                                                                @php($unique_id = rand(1111, 9999))

                                                                <div class="col-sm-12 col-md-4">
                                                                    <div
                                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                            href="{{ route('admin.bhagwan.delete-image', ['id' => $bhagwan['id'], 'name' => $photo]) }}">
                                                                            <i class="tio-delete"></i>
                                                                        </a>

                                                                        <div
                                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                                alt=""
                                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                                src="{{ getValidImage(path: 'storage/app/public/bhagwan/' . $photo, type: 'backend-product') }}">
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
                                                            @foreach (json_decode($bhagwan['images']) as $key => $photo)
                                                                @php($unique_id = rand(1111, 9999))

                                                                <div class="col-sm-12 col-md-4">
                                                                    <div
                                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                            href="{{ route('admin.bhagwan.delete-image', ['id' => $bhagwan['id'], 'name' => $photo]) }}">
                                                                            <i class="tio-delete"></i>
                                                                        </a>

                                                                        <div
                                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                                alt=""
                                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                                src="{{ getValidImage(path: 'storage/app/public/bhagwan/' . $photo, type: 'backend-bhagwan') }}">
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

                                    <div class="additional_image_column col-md-12">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                    <div>
                                                        <label for="name"
                                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_wallpapers') }}</label>
                                                        <span
                                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                            title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span>
                                                    </div>

                                                </div>
                                                <p class="text-muted">{{ translate('upload_wallpapers_for_bhagwan') }}</p>
                                                <div class="coba-area">

                                                <div class="row g-2" id="additional_Wallpaper_Section">
                                                    @foreach (json_decode($bhagwan['wallpapers'] ?? '[]') as $key => $photo)
                                                        @php($unique_id = rand(1111, 9999))
                                                        <div class="col-sm-12 col-md-4">
                                                            <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                href="{{ route('admin.bhagwan.delete-image', ['id' => $bhagwan['id'], 'name' => $photo]) }}">
                                                                    <i class="tio-delete"></i>
                                                                </a>

                                                                <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                    <img id="additional_Image_{{ $unique_id }}"
                                                                        alt=""
                                                                        class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                        src="{{ getValidImage(path: 'storage/app/public/bhagwan/wallpaper/' . $photo, type: 'backend-bhagwan') }}">
                                                                </div>

                                                                <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                                                        <img alt=""
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                            class="w-75">
                                                                        <h3 class="text-muted">{{ translate('upload_wallpapers') }}</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    {{-- Upload input --}}
                                                    <div class="col-sm-12 col-md-4">
                                                        <div class="custom_upload_input position-relative border-dashed-2">
                                                            <input type="file" name="wallpapers[]"
                                                                class="custom-upload-input-file action-add-more-image"
                                                                data-index="1"
                                                                data-imgpreview="additional_Wallpaper_1"
                                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                data-target-section="#additional_Wallpaper_Section">

                                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                <img id="additional_Wallpaper_1" class="h-auto aspect-1 bg-white d-none" alt="" src="">
                                                            </div>

                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt=""
                                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                        class="w-75">
                                                                    <h3 class="text-muted">{{ translate('upload_wallpapers') }}</h3>
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

                                <input type="hidden" id="images" value="{{ $bhagwan->images }}">
                                <input type="hidden" id="wallpaper" value="{{ $bhagwan->wallpaper }}">
                                <input type="hidden" id="bhagwan_id" value="{{ $bhagwan->id }}">
                                <input type="hidden" id="remove_url"
                                    value="{{ route('admin.bhagwan.delete-image', ['id' => $bhagwan['id'], 'name' => $photo]) }}">
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <span id="image-path-of-product-upload-icon"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-want-to-add-or-update-this-bhagwan"
        data-text="{{ translate('want_to_update_this_bhagwan') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-bhagwan-added-successfully" data-text="{{ translate('bhagwan_added_successfully') }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

    <script type="text/javascript">
        $('.bhagwan-add-requirements-check').on('click', function() {
            getbhagwanAddRequirementsCheck()
        });
    </script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>

    <script>
    "use strict";

    let imageCount = {{ 15 - count(json_decode($bhagwan->images)) }};
    let wallpaperCount = {{ 15 - count(json_decode($bhagwan->wallpapers ?? '[]')) }};
    let thumbnail =
        '{{ $bhagwan->thumbnail ? productImagePath('thumbnail') . '/' . $bhagwan->thumbnail : dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}';

    $(function() {
        // Additional Images
        if (imageCount > 0) {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: imageCount,
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

        // Thumbnail
        $("#thumbnail").spartanMultiImagePicker({
            fieldName: 'image',
            maxCount: 1,
            rowHeight: 'auto',
            groupClassName: 'col-12',
            maxFileSize: '',
            placeholderImage: {
                image: thumbnail,
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

        // Wallpapers
        if (wallpaperCount > 0) {
            $("#wallpapers").spartanMultiImagePicker({
                fieldName: 'wallpapers[]',
                maxCount: wallpaperCount,
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
    });
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weekCheckbox = document.getElementById('week_checkbox');
            const weekSelect = document.getElementById('week_select');

            const selectedWeek = '{{ old("week", $bhagwan->week ?? "") }}'; 

            if (selectedWeek) {
                weekCheckbox.checked = true;
                weekSelect.classList.remove('d-none');
                weekSelect.value = selectedWeek;
            }

            weekCheckbox.addEventListener('change', function() {
                if (weekCheckbox.checked) {
                    weekSelect.classList.remove('d-none');
                } else {
                    weekSelect.classList.add('d-none');
                }
            });
        });
    </script>


@endpush

