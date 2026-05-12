@extends('layouts.back-end.app')

@section('title', translate('add_ads_Trust'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('add_ads_Trust') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new add_ads_Trust -->
        <div class="col-md-12 mb-3">

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.donate_management.ad_trust.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($languages as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <div class="col-md-12">
                                @foreach($languages as $k1=>$lang)
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="name">{{ translate('Name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control" value="{{ old('name.'.$loop->index)}}" id="{{$lang}}_name" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="type">{{ translate('Donate_Type') }}<span class="text-danger">*</span></label>
                                            <select name="type" class="form-control fillupdata " data-point='9' onchange="$(`.fillupdata[data-point='9']`).val(this.value);handleTypes_change(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Donate Type</option>
                                                <option value="outsite" {{ ((old('type') == 'outsite')?"selected":"")}}>outsite</option>
                                                <option value="inhouse" {{ ((old('type') == 'inhouse')?"selected":"")}}>inhouse</option>

                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group mt-2 set_types_display {{ ((old('type') == 'outsite')?'':'d-none')}}">
                                            <label class="title-color" for="category_name">{{ translate('Select_category') }}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control fillupdata " data-point='1' onchange="$(`.fillupdata[data-point='1']`).val(this.value);getTrustList(this.value)">
                                                <option value="">Select Category</option>
                                                @if($all_categorys)
                                                @foreach($all_categorys as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('category_id') == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-3 form-group mt-2 set_types_display {{ ((old('type') == 'outsite')?'':'d-none')}}">
                                            <label class="title-color" for="trust_name">{{ translate('Select_Trust') }}<span class="text-danger">*</span></label>
                                            <select name="trust_id" class="form-control fillupdata " data-point='2' onchange="$(`.fillupdata[data-point='2']`).val(this.value)" data-value="{{ old('trust_id') }}">
                                                <option value="">Select Trust</option>
                                                @if($all_trust)
                                                @foreach($all_trust as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('trust_id') == $vals['id'])?"selected":"")}}>{{ ($vals['trust_name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="title-color" for="trust_name">{{ translate('Select_Purpose') }}<span class="text-danger">*</span></label>
                                            <select name="purpose_id" class="form-control fillupdata " data-point='3' onchange="$(`.fillupdata[data-point='3']`).val(this.value)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Purpose</option>
                                                @if($all_purpose)
                                                @foreach($all_purpose as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('purpose_id') == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <!-- <div class="col-md-12 mt-4">
                                            <div class="row"> -->
                                        <div class="col-md-3 form-group mt-2">
                                            <label class="title-color" for="types">{{ translate('Do you want to add products') }}?<span class="text-danger">*</span></label>
                                            <select name="set_type" class="form-control fillupdata" data-point='5' onchange="$(`.fillupdata[data-point='5']`).val(this.value);handleTypeChange(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="1" {{ ((old('set_type') == 1)?"selected":"")}}>Yes</option>
                                                <option value="0" {{ ((old('set_type') == 0)?"selected":"")}}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="formRowsContainer-{{$lang}}">
                                        <div class="row form-row row_lang_0" id="row-{{$lang}}-0">
                                            <div class="col-md-2 mt-2 set_amount_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_amount_{{$lang}}_0">{{ translate('amount') }}</label>
                                                <input type="text" name="set_amount[{{$lang}}][]" value="{{ old('set_amount.'.$lang.'.0', old('set_amount.0', '')) }}" class="form-control fillupdata" data-point='set_amount_0' onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );" onblur="$(`.fillupdata[data-point='set_amount_0']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 mt-2 set_amount_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_title_{{$lang}}_0">{{ translate('title') }} *({{$lang}})</label>
                                                <input type="text" name="set_title[{{$lang}}][]" value="{{ old('set_title.'.$lang.'.0', old('set_title.0', '')) }}" class="form-control fillupdata" data-point='set_title_{{$lang}}_0' onblur="$(`.fillupdata[data-point='set_title_{{$lang}}_0']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 mt-2 set_amount_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_number_{{$lang}}_0">{{ translate('Enter_number') }}</label>
                                                <input type="text" name="set_number[{{$lang}}][]" value="{{ old('set_number.'.$lang.'.0', old('set_number.0', '')) }}" class="form-control fillupdata" data-point='set_number_0' onblur="$(`.fillupdata[data-point='set_number_0']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 mt-2 set_unit_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_unit_{{$lang}}_0">{{ translate('Select_Unit') }}<span class="text-danger">*</span></label>
                                                <select name="set_unit[{{$lang}}][]" class="form-control fillupdata" data-point='set_unit_0' onchange="$(`.fillupdata[data-point='set_unit_0']`).val(this.value)">
                                                    <option value="">Select Unit</option>
                                                    @if($unit_list)
                                                    @foreach($unit_list as $key=>$va)
                                                    <option value="{{$key}}" {{ (old('set_unit.'.$lang.'.0') == $key || old('set_unit.0') == $key) ? "selected" : "" }}>{{$va}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2 mt-2 set_unit_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="image_product_0">{{ (($k1 == 0)? translate('Product_image') : translate('image_preview') ) }}<span class="text-danger">*</span></label>
                                                <input type="file" name="image_product[]" class="form-control image-upload {{ (($k1 == 0)?'':'d-none') }}" id="image_product_0">
                                                <div class="image-preview-container image_preview_0"></div>
                                            </div>
                                            <div class="col-md-2 mt-2 set_unit_display {{ ((old('set_type') == 1)?'':'d-none')}}">
                                                <label class="w-100">&nbsp;</label>
                                                <a class="btn btn-primary" onclick="addRowBtn()">+</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-12 mt-4">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="title-color" for="details">{{ translate('Enter Requirement Amount') }}</label>
                                                    <input type="text" name="set_requirement_amount" class="form-control set_requirement_amount_class" value="{{ old('set_requirement_amount')}}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.set_requirement_amount_class').val(this.value)">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="title-color" for="details">{{ translate('Enter Date Rang') }}</label>
                                                    <input type="text" name="set_requirement_date_range" class="form-control dateRangePicker" value="{{ old('set_requirement_date_range')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea class='form-control ckeditor' name='description[]'>{{ old('description.'.$loop->index)}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @endforeach



                            </div>
                            <div class="col-md-12 mt-3 rest-part">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('ads_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_ads_image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="image" class="custom-upload-input-file action-upload-color-image image-input" data-imgpreview="pre_frc_certificate" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
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
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $('.image-input').on('change', function() {
        const input = this;
        const imgPreviewId = $(this).data('imgpreview');
        const img = document.getElementById(imgPreviewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (img !== null) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                const imgName = input.files[0].name;
                const closestDataTitleElement = input.closest('[data-title]');
                if (closestDataTitleElement) {
                    closestDataTitleElement.setAttribute("data-title", imgName);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    function handleTypeChange(selectElement) {
        if (selectElement.value == "1") {
            $('.set_amount_display').removeClass('d-none');
            $('.set_unit_display').removeClass('d-none');
        } else {
            $('.set_amount_display').addClass('d-none');
            $('.set_unit_display').addClass('d-none');
        }
    }

    function handleTypes_change(selectElement) {
        if (selectElement.value == "outsite") {
            $('.set_types_display').removeClass('d-none');
        } else {
            $('.set_types_display').addClass('d-none');
        }
    }

    function getTrustList(id) {
        $(".fillupdata[data-point='2']").html('');
        $.ajax({
            url: "{{ route('admin.donate_management.ad_trust.api-donate-trust-list')}}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            success: function(response) {
                if (response.success == 1) {
                    let option = '';
                    $.each(response.data, function(index, value) {
                        option += `<option value='${value['id']}'>${value['trust_name']}</option>`;
                    });
                    $(".fillupdata[data-point='2']").html(option);
                } else {
                    $(".fillupdata[data-point='2']").html('');
                }
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.message);
            }
        });

    }

    $(function() {
        $('.dateRangePicker').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        });
        $('.dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
            $('.dateRangePicker').val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
            $('.dateRangePicker').val('');
        });
    });

    let rowCounter = 0;

    // Function to add a new row
    function addRowBtn() {
        rowCounter++;
        <?php foreach ($languages as $k1 => $lang) { ?>
            const newRow<?php echo $lang; ?> = document.createElement('div');
            newRow<?php echo $lang; ?>.className = 'row form-row row_lang_' + rowCounter;
            newRow<?php echo $lang; ?>.id = `row-<?php echo $lang; ?>-${rowCounter}`;

            newRow<?php echo $lang; ?>.innerHTML = `
            <div class="col-md-2 mt-2 set_amount_display">
                <label class="title-color" for="set_amount_<?php echo $lang; ?>_${rowCounter}">Amount</label>
                <input type="text" name="set_amount[<?php echo $lang; ?>][]" class="form-control fillupdata" data-point='set_amount_${rowCounter}' onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );" onblur=$('.fillupdata[data-point="set_amount_${rowCounter}"]').val(this.value)>
            </div>
            <div class="col-md-2 mt-2 set_amount_display">
                <label class="title-color" for="set_title_<?php echo $lang; ?>_${rowCounter}">Title *(<?php echo $lang; ?>)</label>
                <input type="text" name="set_title[<?php echo $lang; ?>][]" class="form-control fillupdata" data-point='set_title_${rowCounter}'>
            </div>
            <div class="col-md-2 mt-2 set_amount_display">
                <label class="title-color" for="set_number_<?php echo $lang; ?>_${rowCounter}">Enter Number</label>
                <input type="text" name="set_number[<?php echo $lang; ?>][]" class="form-control fillupdata" data-point='set_number_${rowCounter}'  onblur=$('.fillupdata[data-point="set_number_${rowCounter}"]').val(this.value)>
            </div>
            <div class="col-md-2 mt-2 set_unit_display">
                <label class="title-color" for="set_unit_<?php echo $lang; ?>_${rowCounter}">Select Unit<span class="text-danger">*</span></label>
                <select name="set_unit[<?php echo $lang; ?>][]" class="form-control fillupdata" data-point='set_unit_${rowCounter}'  onchange=$('.fillupdata[data-point="set_unit_${rowCounter}"]').val(this.value)>
                    <option value="">Select Unit</option>
                    <?php if (isset($unit_list) && $unit_list): ?>
                        <?php foreach ($unit_list as $key => $va): ?>
                            <option value="<?php echo $key; ?>"><?php echo $va; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2 mt-2 set_unit_display">
                <label class="title-color" for="image_product_${rowCounter}">${(("{{$k1}}" == 0)?'Product Image':'Image preview')}"<span class="text-danger">*</span></label>
                <input type="file" name="image_product[]" class="form-control image-upload ${(("{{$k1}}" == 0)?'':'d-none')}" id="image_product_${rowCounter}">
                <div class="image-preview-container image_preview_${rowCounter}"></div>
            </div>
            <div class="col-md-2 mt-2 set_unit_display">
                <label class="w-100">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-remove-row" onclick="removeRow(${rowCounter})">Remove</button>
            </div>
        `;

            document.getElementById('formRowsContainer-<?php echo $lang; ?>').appendChild(newRow<?php echo $lang; ?>);
        <?php } ?>

        // Add event listener for the new file input
        const newFileInput = document.getElementById(`image_product_${rowCounter}`);
        if (newFileInput) {
            newFileInput.addEventListener('change', function() {
                handleImagePreview(this);
            });
        }
    }


    function removeRow(rowId) {
        const rowElement = $(`.row_lang_${rowId}`);
        if (rowElement) {
            rowElement.remove();
        }
    }

    function handleImagePreview(input) {
        const rowId = input.id.split('_').pop();
        const previewContainer = $(`.image_preview_${rowId}`);
        console.log(previewContainer);

        // Clear existing previews using jQuery
        previewContainer.empty();

        if (input.files) {
            Array.from(input.files).forEach(file => {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewDiv = $('<div>').addClass('image-preview');
                        const img = $('<img>').attr('src', e.target.result).css('max-width', '61%');

                        previewDiv.append(img);
                        previewContainer.append(previewDiv);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('.image-upload');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                handleImagePreview(this);
            });
        });
    });
</script>
@endpush