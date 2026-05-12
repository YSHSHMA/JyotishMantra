
@extends('layouts.back-end.app')
@section('title', translate('gallery_update'))
@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/package.png') }}" alt="">
                {{ translate('gallery_update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.temple.gallery.update', [$gallery['id']]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="temple_id" value="{{ $gallery['temple_id'] }}">
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span
                                            class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                            id="{{ $lang }}-link">
                                            {{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        @foreach ($languages as $lang)
                                            <?php
                                            if (count($gallery['translations'])) {
                                                $translate = [];
                                                foreach ($gallery['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == 'title') {
                                                        $translate[$lang]['title'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="title-color" for="title">{{ translate('title') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="title[]"
                                                        value="{{ $lang == $defaultLanguage ? $gallery['title'] : $translate[$lang]['title'] ?? '' }}"
                                                        class="form-control" id="title"
                                                        placeholder="{{ translate('ex') }} : {{ translate('Title') }}"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                </div>

                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach

                                    </div>
                                    <div class="row">
                                        <div class="additional_image_column col-md-12">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <div>
                                                    <label for="name"
                                                        class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                    <span
                                                        class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer"
                                                        data-toggle="tooltip"
                                                        title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="text-muted">{{ translate('upload_additional_gallery_images') }}</p>
                                            <div class="coba-area">

                                                <div class="row g-2" id="additional_Image_Section">

                                                    @if (is_array($gallery['images']) && count($gallery['images']) == 0)
                                                        @foreach (json_decode($gallery['images']) as $key => $photo)
                                                            @php($unique_id = rand(1111, 9999))

                                                            <div class="col-sm-12 col-md-4">
                                                                <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                                    <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                        href="{{ route('admin.service.delete-image', ['id' => $gallery['id'], 'name' => $photo]) }}">
                                                                        <i class="tio-delete"></i>
                                                                    </a>

                                                                    <div
                                                                        class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                        <img id="additional_Image_{{ $unique_id }}"  alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                            src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}">
                                                                    </div>
                                                                    <div
                                                                        class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div
                                                                            class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                                class="w-75">
                                                                            <h3 class="text-muted">
                                                                                {{ translate('Upload_Image') }}
                                                                            </h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        @foreach (json_decode($gallery['images']) as $key => $photo)
                                                            @php($unique_id = rand(1111, 9999))

                                                            <div class="col-sm-12 col-md-4">
                                                                <div
                                                                    class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                    <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"  onclick="removeGalleryImage(this)" data-id="gallery-{{$gallery['id']}}" data-action="{{ route('admin.temple.gallery.remove_image', ['id' => $gallery['id'], 'key' => $key]) }}">
                                                                        <i class="tio-delete"></i>
                                                                    </a>
                                                                    
                                                                    <div
                                                                        class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                        <img id="additional_Image_{{ $unique_id }}" alt=""   class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                            src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}">
                                                                    </div>
                                                                    <div
                                                                        class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div
                                                                            class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75"
                                                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">
                                                                                {{ translate('Upload_Image') }}
                                                                            </h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                    <div class="col-sm-12 col-md-4">
                                                        <div
                                                            class="custom_upload_input position-relative border-dashed-2">
                                                            <input type="file" name="images[]" onchange="filesManager(this.files)"
                                                                class="custom-upload-input-file action-add-more-image"
                                                                data-index="1" data-imgpreview="additional_Image_1"
                                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                data-target-section="#additional_Image_Section" multiple>

                                                            <span
                                                                class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div
                                                                class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                <img id="additional_Image_1"
                                                                    class="h-auto aspect-1 bg-white d-none"
                                                                    alt="" src="">
                                                            </div>
                                                            <div
                                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div
                                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt=""
                                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                        class="w-75">
                                                                    <h3 class="text-muted">
                                                                        {{ translate('Upload_Image') }}</h3>
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
    <span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script>
       var colors = 1;
        let imageCount = "{{ 15 - count(json_decode($gallery->images)) }}";
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
                    image: "{{ productImagePath('thumbnail') . '/' . $gallery->thumbnail ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}",
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

        function removeGalleryImage(that){
    var id = $(that).data('id');
    let getText = $('#get-confirm-and-cancel-button-text-for-delete');
    Swal.fire({
        title: getText.data('sure'),
        text: getText.data('text'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: getText.data('cancel'),
        confirmButtonText: getText.data('confirm'),
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: $(that).data('action'),
                method: 'DELETE',
                data: {},
                success: function (data) {
                    if($(this).data('from') === 'currency'){
                        if (parseInt(data.status) === 1) {
                            toastr.success($('#get-delete-currency-message').data('success'));
                        } else {
                            toastr.warning($('#get-delete-currency-message').data('warning'));
                        }
                    }else{
                        toastr.success($('#get-deleted-message').data('text'));
                    }
                    location.reload();
                }
            });
        }
    })
}

// document.addEventListener('DOMContentLoaded', function() {
//             const input = document.querySelector('.custom-upload-input-file');
//             const previewContainer = document.querySelector('#additional_Image_Section');
//             input.addEventListener('change', function(event) {
//                 const files = event.target.files;
//                 for (const file of files) {
//                     if (file && file.type.startsWith('image/')) {
//                         const reader = new FileReader();
//                         reader.onload = function(e) {
//                             const img = document.createElement('img');
//                             img.src = e.target.result;
//                             img.classList.add('preview-img');
//                             var html = `<div class="col-sm-12 col-md-4 add_image_news">
//                                                                 <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
//                                                                     <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn">
//                                                                         <i class="tio-delete"></i>
//                                                                     </a>                                                                    
//                                                                     <div class="img_area_with_preview position-absolute z-index-2 border-0">
//                                                                         <img alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none" src="${e.target.result}">
//                                                                     </div>
                                                                    
//                                                                 </div>
//                                                             </div>`;


//                             previewContainer.innerHTML += html;
//                         };
//                         reader.readAsDataURL(file);
//                     }
//                 }
//             });
//         });

document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('.custom-upload-input-file');
            const previewContainer = document.querySelector('#additional_Image_Section');

            input.addEventListener('change', function(event) {
                const files = event.target.files;

                for (const file of files) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const colDiv = document.createElement('div');
                            colDiv.className = 'col-sm-12 col-md-4 add_image_news';

                            const customUploadDiv = document.createElement('div');
                            customUploadDiv.className = 'custom_upload_input custom-upload-input-file-area position-relative border-dashed-2';

                            const deleteLink = document.createElement('a');
                            deleteLink.className = 'delete_file_input_css btn btn-outline-danger btn-sm square-btn';
                            deleteLink.innerHTML = '<i class="tio-delete"></i>';
                            deleteLink.addEventListener('click', function() {
                                previewContainer.removeChild(colDiv);
                            });

                            const imgAreaDiv = document.createElement('div');
                            imgAreaDiv.className = 'img_area_with_preview position-absolute z-index-2 border-0';
                            const imgElement = document.createElement('img');
                            imgElement.className = 'h-auto aspect-1 bg-white onerror-add-class-d-none';
                            imgElement.src = e.target.result;
                            imgAreaDiv.appendChild(imgElement);

                            const placeholderDiv = document.createElement('div');
                            placeholderDiv.className = 'position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center upload-placeholder';
                            const placeholderImg = document.createElement('img');
                            placeholderImg.src = 'http://localhost:8000/assets/back-end/img/icons/product-upload-icon.svg';
                            placeholderImg.alt = '';
                            placeholderImg.className = 'w-75';
                            const placeholderText = document.createElement('h3');
                            placeholderText.className = 'text-muted';
                            placeholderText.textContent = 'Upload Image';
                            placeholderDiv.appendChild(placeholderImg);
                            placeholderDiv.appendChild(placeholderText);

                            customUploadDiv.appendChild(deleteLink);
                            customUploadDiv.appendChild(imgAreaDiv);
                            customUploadDiv.appendChild(placeholderDiv);
                            colDiv.appendChild(customUploadDiv);

                            previewContainer.appendChild(colDiv);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            });
        });

        
    </script>
@endpush
