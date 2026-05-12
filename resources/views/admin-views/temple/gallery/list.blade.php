@extends('layouts.back-end.app')

@section('title', translate('Gallery'))
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/admin/gallery.css') }}" rel="stylesheet">
@push('css_or_js')
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('gallery_list') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <input type="hidden" name="temple_id" value="{{ $id }}">

                                    </div>
                                    <div class="row g-2">
                                        <div class="additional_image_column col-md-12">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <div>
                                                    <label for="name"
                                                        class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                    <span
                                                        class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                        title="{{ translate('upload_any_additional_images_for_this_temple_gallery_from_here') }}.">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                                </div>

                                            </div>
                                            <div id="dropBox">
                                                <p>Drag & Drop Images Here...</p>
                                                <input type="file" id="imgUpload" multiple accept="image/*"
                                                    onchange="filesManager(this.files)" name="images">

                                                <label class="button" for="imgUpload">...or Upload From Your
                                                    Computer</label>
                                                <div id="gallery"></div>
                                            </div>
                                        </div>
                                        {{-- <div class="additional_image_column col-md-12">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <div>
                                                    <label for="name"
                                                        class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                    <span
                                                        class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                        title="{{ translate('upload_any_additional_images_for_this_temple_gallery_from_here') }}.">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                                </div>

                                            </div>
                                            <p class="text-muted">
                                                {{ translate('upload_additional_temple_gallery_images') }}</p>

                                            <div class="row g-2" id="additional_Image_Section">
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="custom_upload_input position-relative border-dashed-2">
                                                        <input type="file" name="images[]"
                                                            class="custom-upload-input-file action-add-more-image"
                                                            data-index="1" data-imgpreview="additional_Image_1"
                                                            accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                            data-target-section="#additional_Image_Section" multiple>
                                                        <span
                                                            class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                            <i class="tio-delete"></i>
                                                        </span>

                                                        <div
                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_1"
                                                                class="h-auto aspect-1 bg-white d-none "
                                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}"
                                                                alt="">
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

                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('gallery_view') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $gallery->count() }}</span>
                                </h5>
                            </div>
                        </div>
                        <p>
                        <section class="img-gallery-magnific">
                            @foreach ($gallery as $key => $item)
                                <div class="magnific-img">
                                    @foreach (json_decode($item->images) as $key => $photo)
                                        <a class="image-popup-vertical-fit"
                                            href="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}"
                                            title="9.jpg">
                                            <img src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}"
                                                alt="9.jpg" />
                                            <i class="fa fa-search-plus" aria-hidden="true"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </section>
                        <div class="clear"></div>
                        </p>
                        {{-- <div class="gallery">
                            @foreach ($gallery as $key => $item)
                            @foreach (json_decode($item->images) as $key => $photo)
                            <img src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}" alt="">
                            @endforeach
                            @endforeach
                        </div> --}}

                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $gallery->links() }}
                        </div>
                    </div>
                    @if (count($gallery) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="{{ translate('image_description') }}">
                            <p class="mb-0">{{ translate('no_data_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <span id="image-path-of-product-upload-icon"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    <script type="text/javascript">
        $('.delete_file_input').on('click', function() {
            let $parentDiv = $(this).parent().parent();
            $parentDiv.find('input[type="file"]').val('');
            $parentDiv.find('.img_area_with_preview img').addClass("d-none");
            $(this).removeClass('d-flex');
            $(this).hide();
        });
        // gallery
        $('.image-popup-vertical-fit').magnificPopup({
            type: 'image',
            mainClass: 'mfp-with-zoom',
            gallery: {
                enabled: true
            },

            zoom: {
                enabled: true,
                duration: 300, // duration of the effect, in milliseconds
                easing: 'ease-in-out', // CSS transition easing function
                opener: function(openerElement) {
                    return openerElement.is('img') ? openerElement : openerElement.find('img');
                }
            }

        });
    </script>
    <script>
        let dropBox = document.getElementById('dropBox');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
            dropBox.addEventListener(evt, prevDefault, false);
        });
        function prevDefault(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        ['dragenter', 'dragover'].forEach(evt => {
            dropBox.addEventListener(evt, hover, false);
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropBox.addEventListener(evt, unhover, false);
        });
        function hover(e) {
            dropBox.classList.add('hover');
        }
        function unhover(e) {
            dropBox.classList.remove('hover');
        }
        dropBox.addEventListener('drop', mngDrop, false);
        function mngDrop(e) {
            let dataTrans = e.dataTransfer;
            let files = dataTrans.files;
            filesManager(files);
        }
        function upFile(file) {
            let imageType = /images.*/;
            if (file.type.match(imageType)) {
                let url = '{{ route('admin.temple.gallery.store') }}';
                let formData = new FormData();
                formData.append('file', file);
                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        console.log('Success:', result);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
                console.error("Only images are allowed!", file);
            }
        }
        function previewFile(file) {
            let imageType = /image.*/;
            if (file.type.match(imageType)) {
                let fReader = new FileReader();
                let gallery = document.getElementById('gallery');
                fReader.readAsDataURL(file);
                fReader.onloadend = function() {
                    let wrap = document.createElement('div');
                    let img = document.createElement('img');
                    img.src = fReader.result;
                    let imgCapt = document.createElement('p');
                    let fSize = (file.size / 1000) + ' KB';
                    imgCapt.innerHTML =
                        `<span class="fName">${file.name}</span><span class="fSize">${fSize}</span><span class="fType">${file.type}</span>`;
                    gallery.appendChild(wrap).appendChild(img);
                    gallery.appendChild(wrap).appendChild(imgCapt);
                }
            } else {
                console.error("Only images are allowed!", file);
            }
        }
        function filesManager(files) {
            files = [...files];
            files.forEach(upFile);
            files.forEach(previewFile);
        }
    </script>
@endpush
