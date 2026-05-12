@extends('layouts.back-end.app')

@section('title', translate('Cab Update'))
@push('css_or_js')
<style>
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Cab Update') }}
        </h2>
    </div>
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.tour_and_travels.cab.cab-edit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="title-color" for="name">{{ translate('select_cab') }}<span class="text-danger">*</span></label>
                            <select name="cab_id" class="form-control">
                                <option value="">{{ translate('select_cab') }}</option>
                                @if($carlists)
                                @foreach($carlists as $va)
                                <option value="{{ $va['id']}}" {{ ((old('cab_id',$traveller_data['cab_id']) == $va['id'] )?"selected" :"" ) }}>{{ $va['name'] }}</option>
                                @endforeach
                                @endif
                            </select>
                            <input type="hidden" name="id" value="{{ $traveller_data['id'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="reg_number">{{ translate('reg_number') }}</label>
                            <input type="text" name="reg_number" value="{{old('reg_number',$traveller_data['reg_number'])}}" class="form-control" placeholder="{{ translate('enter_register_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="model_number">{{ translate('model_number') }}</label>
                            <input type="text" name="model_number" value="{{old('model_number',$traveller_data['model_number']) }}" class="form-control" placeholder="{{ translate('enter_model_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="fuel_type">{{ translate('fuel_type') }}</label>
                            <select name="fuel_type" class="form-control" required>
                                <option value="">Select Fuel Type</option>
                                <option value="petrol" {{ (('petrol' == old('fuel_type',$traveller_data['fuel_type']) )?'selected':'')}}>Petrol</option>
                                <option value="diesel" {{ (('diesel' == old('fuel_type',$traveller_data['fuel_type']) )?'selected':'')}}>Diesel</option>
                                <option value="cng" {{ (('cng' == old('fuel_type',$traveller_data['fuel_type']) )?'selected':'')}}>CNG</option>
                                <option value="electric" {{ (('electric' == old('fuel_type',$traveller_data['fuel_type']) )?'selected':'')}}>Electric</option>
                                <option value="hybrid" {{ (('hybrid' == old('fuel_type',$traveller_data['fuel_type']) )?'selected':'')}}>Hybrid</option>
                            </select>
                        </div>
                        <div class="additional_image_column col-md-12 mt-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label for="name"
                                                class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                            <span
                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                title="{{ translate('upload_any_additional_images_for_this_vehicle_from_here') }}.">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                    alt="">
                                            </span>
                                        </div>

                                    </div>
                                    <p class="text-muted">{{ translate('upload_additional_vehicle_images') }}</p>
                                    <div class="coba-area">

                                        <div class="row g-2" id="additional_Image_Section">

                                            @if (!empty($traveller_data['image']) && json_decode($traveller_data['image'],true))
                                            @foreach (json_decode($traveller_data['image'],true) as $key => $photo)
                                            @php($unique_id = rand(1111, 9999))

                                            <div class="col-sm-12 col-md-4">
                                                <div
                                                    class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                    <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                        href="{{ route('admin.tour_and_travels.cab.delete-image', ['id' => $traveller_data['id'], 'name' => $photo]) }}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                    <div
                                                        class="img_area_with_preview position-absolute z-index-2 border-0">
                                                        <img id="additional_Image_{{ $unique_id }}"
                                                            alt=""
                                                            class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                            src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_cab/' . $photo, type: 'backend-product') }}">
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

                                            <div class="col-sm-12 col-md-4">
                                                <div class="custom_upload_input position-relative border-dashed-2">
                                                    <input type="file" name="image[]"
                                                        class="custom-upload-input-file action-add-more-image" data-index="1"
                                                        data-imgpreview="additional_Image_1"
                                                        accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                        data-target-section="#additional_Image_Section">

                                                    <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
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
                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-2">
                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--  -->

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')

<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush