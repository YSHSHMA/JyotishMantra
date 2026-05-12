@extends('layouts.back-end.app')

@section('title', translate('add_self_driving'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('add_self_driving') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new add_self_driving -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.self-driving-management.self-driving-store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Language tabs -->
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
                                <!-- Input fields for tour package name -->
                                @foreach($languages as $lang)
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="title-color" for="name">{{ translate('Select_Type') }}</label>
                                            <select name="type" id="" class="form-control type_id" required onchange="getCategories();$('.type_id').val(this.value)">
                                                <option value="">{{ translate('Select_Type') }}</option>
                                                @if($typeList)
                                                @foreach($typeList as $va)
                                                <option value="{{ $va['id']}}" {{ ((old('type') == $va['id'])?'selected':'')}}>{{ $va['type']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="title-color" for="name">{{ translate('select_category') }}</label>
                                            <select name="category_id" id="" class="form-control type_vehicle_cateogry_id" onchange="getVehicles();$('.type_vehicle_cateogry_id').val(this.value)" required>
                                                <option value="">{{ translate('select_category') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="title-color" for="name">{{ translate('select_cab') }}</label>
                                            <select name="cab_id" id="" class="form-control vehicle_list" onchange="$('.vehicle_list').val(this.value)" required>
                                                <option value="">{{ translate('select_cab') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="title-color" for="name">{{ translate('select_traveller') }}</label>
                                            <select name="traveller_id" class="form-control traveller_ids" onchange="$('.traveller_ids').val(this.value)" required>
                                                <option value="0">{{ translate('All_Traveller') }}</option>
                                                @if($travelar_list)
                                                @foreach($travelar_list as $va)
                                                <option value="{{ $va['id'] }}" {{ ((old('traveller_id') == $va['id'])?'selected':'')}}>{{ $va['company_name'] }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('select_air_conditioning_status') }}</label>
                                            <select name="air_conditioning_status" id="" class="form-control air_conditioning_status" onchange="$('.air_conditioning_status').val(this.value)" required>
                                                <option value="1" selected>Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('select_cab_type') }}</label>
                                            <select name="car_type" id="" class="form-control car_type" onchange="$('.car_type').val(this.value)" required>
                                                <option value="manual">Manual</option>
                                                <option value="automatic">Automatic</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('basic_price') }}</label>
                                            <input type="text" name="basic_price" class="form-control basic_prices" value="{{ old('basic_price')}}" onchange="$('.basic_prices').val(this.value)" placeholder="{{ translate('Enter_basic_price') }}" required onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 ">
                                            <button type="button" class="btn btn-success my-3 float-end add-cab-about" data-lang="{{ $lang }}">+ About Cab Info</button>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12" id="cab-about-wrapper-{{ $lang }}">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 ">
                                            <button type="button" class="btn btn-success my-3 float-end add-parent-btn" data-lang="{{ $lang }}">+ Extra Policy Add</button>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12" id="vip-darshan-wrapper-{{ $lang }}">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('Drivers age requirements') }}</label>
                                            <textarea name="drivers_age_details[]" class="form-control drivers_age_details ckeditor" onkeyup="$('.drivers_age_details').val(this.value)">{{ old('drivers_age_details.'.$loop->index)}}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('Tips for drivers') }}</label>
                                            <textarea name="tip_for_driving[]" class="form-control tip_for_driving ckeditor" onkeyup="$('.tip_for_driving').val(this.value)">{{ old('tip_for_driving.'.$loop->index)}}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="title-color my-3 font-weight-bolder" for="name">{{ translate('Required documents for pick up') }}</label>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('I am not a local resident') }}</label>
                                            <textarea name="not_local_resident[]" class="form-control not_local_resident ckeditor" onkeyup="$('.not_local_resident').val(this.value)">{{ old('not_local_resident.'.$loop->index)}}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('I am a local resident') }}</label>
                                            <textarea name="local_resident[]" class="form-control local_resident ckeditor" onkeyup="$('.local_resident').val(this.value)">{{ old('local_resident.'.$loop->index)}}</textarea>
                                        </div>
                                    </div>
                                    <div class="row language-block" data-lang="{{ $lang }}">
                                        <div class="col-md-12">
                                            <label class="my-3 font-weight-bolder" for="name">{{ translate('pickUp_point') }} ({{ $lang }})</label>
                                        </div>
                                        @php
                                        $locations = old('location.' . $lang, ['']);
                                        @endphp
                                        @foreach ($locations as $index => $location)
                                        <div class="col-md-5 mb-2">
                                            <input type="text"
                                                name="location[{{ $lang }}][]"
                                                class="form-control"
                                                value="{{ $location }}"
                                                placeholder="{{ translate('Enter_location') }}"
                                                required>
                                        </div>
                                        @endforeach

                                        <div class="col-md-1 p-0 mb-2">
                                            <button class="btn btn-sm btn-primary add-location" data-lang="{{ $lang }}" type="button">
                                                <i class="tio-add"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-12">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('theme_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_theme_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="theme_image" multiple class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_tour_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_tour_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
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
                                                <p class="text-muted">{{ translate('upload_additional_service_images') }}</p>
                                                <div class="row g-2" id="additional_Image_Section">
                                                    <div class="col-sm-12 col-md-4">
                                                        <div class="custom_upload_input position-relative border-dashed-2">
                                                            <input type="file" name="images[]" class="custom-upload-input-file action-add-more-image" data-index="1" data-imgpreview="additional_Image_1" accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*" data-target-section="#additional_Image_Section">

                                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
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
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
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
<script>
    function getCategories() {
        $.ajax({
            url: "{{ route('admin.tour_cab_service.vehicle_category') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('.type_id').val()
            },
            success: function(response) {
                if (response.success == 1) {
                    let html = `<option value="" selected disabled>{{ translate('select_vehicle_category') }}</option>`;
                    let oldCategoryId = "{{ old('category_id') }}";
                    response.data.forEach(function(item) {
                        let selected = item.id == oldCategoryId ? 'selected' : '';
                        html += `<option value="${item.id}" ${selected}>${item.brand_name}</option>`;
                    });
                    $('.type_vehicle_cateogry_id').html(html);
                } else {
                    toastr.error(response.message, '', {
                        positionClass: 'toast-bottom-left'
                    });
                }
            }
        })
    }

    function getVehicles() {
        $.ajax({
            url: "{{ route('admin.self-driving-management.get-cab-list') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('.type_vehicle_cateogry_id').val()
            },
            success: function(response) {
                if (response.success == 1) {
                    let html = `<option value="" selected disabled>{{ translate('select_cab') }}</option>`;
                    let oldCategoryId = "{{ old('cab_id') }}";
                    response.data.forEach(function(item) {
                        let selected = item.id == oldCategoryId ? 'selected' : '';
                        html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                    });
                    $('.vehicle_list').html(html);
                } else {
                    toastr.error(response.message, '', {
                        positionClass: 'toast-bottom-left'
                    });
                }
            }
        })
    }
</script>
<script>
    let cababoutIndex = 0;
    const allLanguages = @json($languages);

    $(document).on('click', '.add-cab-about', function() {
        const currentLang = $(this).data('lang');

        // Loop through all languages and add the field
        allLanguages.forEach(function(lang) {
            let html = `
            <div class="parent-cab-abouts border p-3 mb-3 bg-light rounded" data-lang="${lang}" data-index="${cababoutIndex}">
                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">About (${lang})</label>
                    <div class="col-sm-7">
                        <input type="text" name="cababout[${lang}][${cababoutIndex}][name]" class="form-control mb-2" placeholder="Enter About title">
                        <input type="text" name="cababout[${lang}][${cababoutIndex}][details]" class="form-control" placeholder="Enter About details">
                    </div>
                    <div class="col-sm-3 d-flex">
                        <button type="button" class="btn btn-danger remove-parent">−</button>
                    </div>
                </div>
            </div>
        `;
            $(`#cab-about-wrapper-${lang}`).append(html);
        });

        cababoutIndex++;
    });

    // Remove logic: remove all language versions with same index
    $(document).on('click', '.remove-parent', function() {
        const parent = $(this).closest('.parent-cab-abouts');
        const index = parent.data('index');
        allLanguages.forEach(function(lang) {
            $(`#cab-about-wrapper-${lang} .parent-cab-abouts[data-index="${index}"]`).remove();
        });
    });
</script>
<script>
    let parentIndex = 0;

    $(document).on('click', '.add-parent-btn', function() {
        const langClicked = $(this).data('lang');

        allLanguages.forEach(function(lang) {
            const html = `
            <div class="parent-vip-darshan border p-3 mb-3 bg-light rounded" data-index="${parentIndex}">
                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Policy Name (${lang})</label>
                    <div class="col-sm-4">
                        <input type="text" name="policyinfo[${lang}][${parentIndex}][name]" class="form-control" placeholder="Enter Policy Name">
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="policyinfo[${lang}][${parentIndex}][price]" class="form-control parent-input policy_infomation${parentIndex}" onkeyup="$('.policy_infomation${parentIndex}').val(this.value)" placeholder="Enter Policy Total Amount">
                    </div>
                    <div class="col-sm-3 d-flex">
                        <button type="button" class="btn btn-primary mr-2 add-child" data-lang="${lang}">+ Add policy Info</button>
                        <button type="button" class="btn btn-danger remove-parent">−</button>
                    </div>
                </div>
                <div class="child-wrapper pl-3"></div>
            </div>
        `;

            $(`#vip-darshan-wrapper-${lang}`).append(html);
        });

        parentIndex++;
    });

    // Remove Policy Block in all languages
    $(document).on('click', '.remove-parent', function() {
        const index = $(this).closest('.parent-vip-darshan').data('index');
        allLanguages.forEach(function(lang) {
            $(`#vip-darshan-wrapper-${lang} .parent-vip-darshan[data-index="${index}"]`).remove();
        });
    });

    // Add Child to Specific Policy Block (per language)
    // Add Child to all languages for the same parent block
    $(document).on('click', '.add-child', function() {
        const langClicked = $(this).data('lang'); // the language in which the button was clicked
        const parentBlock = $(this).closest('.parent-vip-darshan');
        const parentIndex = parentBlock.data('index');

        // Get current number of children (from the clicked language's wrapper)
        const currentChildCount = $(`#vip-darshan-wrapper-${langClicked} .parent-vip-darshan[data-index="${parentIndex}"] .child-wrapper`).children().length;

        // Now add same child block for all languages
        allLanguages.forEach(function(lang) {
            const childWrapper = $(`#vip-darshan-wrapper-${lang} .parent-vip-darshan[data-index="${parentIndex}"] .child-wrapper`);
            const childHtml = `
            <div class="child-vip-darshan border p-2 mt-2 bg-white rounded" data-child-index="${currentChildCount}">
                <div class="form-group row align-items-center">
                    <label class="col-sm-3 col-form-label">Policy (${lang})</label>
                    <div class="col-sm-6">
                        <input type="text" name="policyinfo[${lang}][${parentIndex}][children][${currentChildCount}][name]" class="form-control child-input" placeholder="Enter policy Info">
                    </div>
                    <div class="col-sm-3 d-flex p-0">
                        <button type="button" class="btn btn-danger remove-child" data-lang="${lang}" data-parent-index="${parentIndex}" data-child-index="${currentChildCount}">−</button>
                    </div>
                </div>
            </div>
        `;
            childWrapper.append(childHtml);
        });
    });


    // Remove child field across languages by parentIndex and childIndex
    $(document).on('click', '.remove-child', function() {
        const lang = $(this).data('lang');
        const parentIndex = $(this).data('parent-index');
        const childIndex = $(this).data('child-index');

        allLanguages.forEach(function(l) {
            $(`#vip-darshan-wrapper-${l} .parent-vip-darshan[data-index="${parentIndex}"] .child-vip-darshan[data-child-index="${childIndex}"]`).remove();
        });
    });

    // Only allow integer input for price
    document.addEventListener('input', function(e) {
        if (e.target.matches('.parent-input')) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        }
    });
</script>
<script>
    document.querySelectorAll('.add-location').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.language-block').forEach(function(container) {
                let lang = container.getAttribute('data-lang');
                if (!lang) return;

                let rowDiv = document.createElement('div');
                rowDiv.classList.add('col-md-5', 'mb-2', 'd-flex', 'align-items-start', 'location-input-group');

                rowDiv.innerHTML = `
                    <input type="text"
                           name="location[${lang}][]"
                           class="form-control"
                           placeholder="Enter location"
                           required>
                    <button type="button" class="btn btn-danger btn-sm ml-2 remove-location" title="Remove">
                        <i class="tio-clear"></i>
                    </button>
                `;

                let addBtnDiv = container.querySelector('.add-location').closest('.col-md-1');
                container.insertBefore(rowDiv, addBtnDiv);

                // Attach remove event
                rowDiv.querySelector('.remove-location').addEventListener('click', function() {
                    rowDiv.remove();
                });
            });
        });
    });
</script>


@endpush