@extends('layouts.back-end.app-trustees')
@section('title', translate('temple_gallery'))
@php
use App\Utils\Helpers;
@endphp
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('tample_gallery') }}
        </h2>
    </div>
    <form action="{{ route('trustees-vendor.vip-darshan.update-gallery-image',[$gallery['id']]) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="additional_image_column">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="coba-area">
                                <div class="row g-2" id="additional_Image_Section">
                                    @if (!empty($gallery['images']) && json_decode($gallery['images'],true))
                                    @foreach (json_decode($gallery['images'],true) as $key => $photo)
                                    @php($unique_id = rand(1111, 9999))
                                    <div class="col-sm-12 col-md-3">
                                        <div
                                            class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                            @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'gallery remove'))
                                            <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                href="{{ route('trustees-vendor.vip-darshan.delete-image', ['id' => $gallery['id'], 'name' => $photo]) }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                <img id="additional_Image_{{ $unique_id }}" alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                    src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'backend-product') }}">                                                    
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
    
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary float-end" type="submit">Save</button>
                                    </div>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

@endpush