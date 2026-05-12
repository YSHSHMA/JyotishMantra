@extends('layouts.back-end.app')

@section('title', translate('Gallery'))
@push('css_or_js')
<style>
.gallery{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1vh;
    margin-inline: auto;
    max-width: 100%;
    padding: 1vh;
}

 .gallery > img{
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    transition: all 0.3s ease;
}

.gallery:has(img:hover) img:not(:hover) {
    scale: 0.8;
    opacity: 0.5;
    filter: grayscale(70%);
}
</style>
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
                        <form action="{{ route('admin.temple.gallery.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span
                                            class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                            id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <input type="hidden" name="temple_id" value="{{ $id }}">
                                        @foreach ($languages as $lang)
                                            <div class="form-group {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('gallery_title') }}<span
                                                            class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                    <input type="text" name="title[]" class="form-control"
                                                        placeholder="{{ translate('gallery_title') }}"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                </div>


                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
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
                                                        <span  class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                            <i class="tio-delete"></i>
                                                        </span>

                                                        <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none "
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

                                        </div>
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
                            
                            <div class="float-end">
                                
                            </div>

                        </div>

                        <div class="gallery">
                            @foreach ($gallery as $key => $item)
                            @foreach (json_decode($item->images) as $key => $photo)
                            <img src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}" alt="">
                            @endforeach
                            @endforeach
                        </div>

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
    </script>
@endpush
