@extends('layouts.back-end.app')

@section('title', translate('update_ads_Trust'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('update_ads_Trust') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new update_ads_Trust -->
        <div class="col-md-12 mb-3">

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.donate_management.ad_trust.updatestore') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            <input type='hidden' name='id' value="{{$old_data['id']}}">
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
                                <?php
                                $translate = [];
                                if (!empty($old_data['translations'])) {
                                    foreach ($old_data['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'name') {
                                            $translate[$lang]['name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="name">{{ translate('Name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control" value="{{ old('name.'.$loop->index,(($lang == $defaultLanguage)?$old_data['name']: $translate[$lang]['name']))}}" id="{{$lang}}_name" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="type">{{ translate('Donate_Type') }}<span class="text-danger">*</span></label>
                                            <select name="type" class="form-control fillupdata " data-point='9' onchange="$(`.fillupdata[data-point='9']`).val(this.value);handleTypes_change(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Donate Type</option>
                                                <option value="outsite" {{ ((old('type',$old_data['type']) == 'outsite')?"selected":"")}}>outsite</option>
                                                <option value="inhouse" {{ ((old('type',$old_data['type']) == 'inhouse')?"selected":"")}}>inhouse</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group set_types_display {{ ((old('type',$old_data['type']) == 'outsite')?'':'d-none')}}">
                                            <label class="title-color" for="category_name">{{ translate('Select_category') }}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control fillupdata " data-point='1' onchange="$(`.fillupdata[data-point='1']`).val(this.value);getTrustList(this.value)">
                                                <option value="">Select Category</option>
                                                @if($all_categorys)
                                                @foreach($all_categorys as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('category_id',$old_data['category_id']) == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-3 form-group set_types_display {{ ((old('type',$old_data['type']) == 'outsite')?'':'d-none')}}">
                                            <label class="title-color" for="trust_name">{{ translate('Select_Trust') }}<span class="text-danger">*</span></label>
                                            <select name="trust_id" class="form-control fillupdata " data-point='2' onchange="$(`.fillupdata[data-point='2']`).val(this.value)" data-value="{{ old('trust_id',$old_data['trust_id']) }}">
                                                <option value="">Select Trust</option>
                                                @if($all_trust)
                                                @foreach($all_trust as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('trust_id',$old_data['trust_id']) == $vals['id'])?"selected":"")}}>{{ ($vals['trust_name']??"")}}</option>
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
                                                <option value="{{ $vals['id']}}" {{ ((old('purpose_id',$old_data['purpose_id']) == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <!-- <div class="col-md-12 mt-4">
                                            <div class="row"> -->
                                        <div class="col-md-3 form-group">
                                            <label class="title-color" for="types">{{ translate('Select_Types') }}<span class="text-danger">*</span></label>
                                            <select name="set_type" class="form-control fillupdata" data-point='5' onchange="$(`.fillupdata[data-point='5']`).val(this.value);handleTypeChange(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Types</option>
                                                <option value="1" {{ ((old('set_type',$old_data['set_type']) == 1)?"selected":"")}}>Add</option>
                                                <option value="0" {{ ((old('set_type',$old_data['set_type']) == 0)?"selected":"")}}>No Use</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mt-2 set_amount_display {{ ((old('set_type',$old_data['set_type']) == 1)?'':'d-none')}}">
                                            <label class="w-100">&nbsp;</label>
                                            <a class="btn btn-primary" onclick="addRowBtn()">+</a>
                                        </div>
                                    </div>
                                    <div id="formRowsContainer-{{$lang}}">
                                        @php
                                        $setJson = json_decode($old_data['set_json'] ?? '{}', true);
                                        $langData = $setJson[$lang] ?? [];
                                        @endphp
                                        @if(!empty($langData))
                                        @foreach($langData as $index => $row)
                                        <div class="row form-row row_lang_{{ $index }}" id="row-{{$lang}}-{{ $index }}">
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_amount_{{$lang}}_{{ $index }}">{{ translate('amount') }}</label>
                                                <input type="text" name="set_amount[{{$lang}}][]" value="{{ old('set_amount.'.$lang.'.'.$index, $row['set_amount'] ?? '') }}" class="form-control fillupdata" data-point='set_amount_{{ $index }}' onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );" onblur="$(`.fillupdata[data-point='set_amount_{{ $index }}']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_title_{{$lang}}_{{ $index }}">{{ translate('title') }} *({{$lang}})</label>
                                                <input type="text" name="set_title[{{$lang}}][]" value="{{ old('set_title.'.$lang.'.'.$index, $row['set_title'] ?? '') }}" class="form-control fillupdata" data-point='set_title_{{$lang}}_{{ $index }}' onblur="$(`.fillupdata[data-point='set_title_{{$lang}}_{{ $index }}']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_number_{{$lang}}_{{ $index }}">{{ translate('Enter_number') }}</label>
                                                <input type="text" name="set_number[{{$lang}}][]" value="{{ old('set_number.'.$lang.'.'.$index, $row['set_number'] ?? '') }}" class="form-control fillupdata" data-point='set_number_{{ $index }}' onblur="$(`.fillupdata[data-point='set_number_{{ $index }}']`).val(this.value)">
                                            </div>
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="set_unit_{{$lang}}_{{ $index }}">{{ translate('Select_Unit') }}<span class="text-danger">*</span></label>
                                                <select name="set_unit[{{$lang}}][]" class="form-control fillupdata" data-point='set_unit_{{ $index }}' onchange="$(`.fillupdata[data-point='set_unit_{{ $index }}']`).val(this.value)">
                                                    <option value="">Select Unit</option>
                                                    @if($unit_list)
                                                    @foreach($unit_list as $key=>$va)
                                                    <option value="{{$key}}" {{ (old('set_unit.'.$lang.'.'.$index, $row['set_unit'] ?? '') == $key) ? "selected" : "" }}>{{$va}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <label class="title-color" for="image_product_{{ $k1 }}">
                                                    {{ ($k1 == 0) ? translate('Product_image') : translate('image_preview') }}<span class="text-danger">*</span>
                                                </label>
                                                @if($k1 == 0)
                                                <input type="file" name="image_product[{{ $index }}]" class="form-control image-upload" id="image_product_{{ $index }}">
                                                @endif
                                                <div class="image-preview-container image_preview_{{ $index }}">
                                                    @if(isset($row['image']))
                                                    <div class="image-preview">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/'.$row['image'], type: 'backend-product')  }}" style="max-width: 61%;" alt="Preview">
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group set_amount_display {{ ((old('set_type', $old_data['set_type'] ?? '') == 1)?'':'d-none')}}">
                                                <input type="hidden" name="existing_images[{{ $index }}]" value="{{ (($row['image']??'') ? ($row['image']??'null') : 'null') }}">
                                                <label class="w-100">&nbsp;</label>
                                                @if($index == 0)
                                                <a class="btn btn-primary" onclick="addRowBtn()">+</a>
                                                @else
                                                <button type="button" class="btn btn-danger btn-remove-row" onclick="removeRow({{ $index }})">{{ translate('Remove') }}</button>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="row">
                                        <!-- </div>
                                        </div> -->
                                        <div class="col-md-12 mt-4">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="title-color" for="details">{{ translate('Enter Requirement Amount') }}</label>
                                                    <input type="text" name="set_requirement_amount" class="form-control set_requirement_amount_class" autocomplete="off" value="{{ old('set_requirement_amount',($old_data['set_requirement_amount']??''))}}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.set_requirement_amount_class').val(this.value)">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="title-color" for="details">{{ translate('Enter Date Rang') }}</label>
                                                    <input type="text" name="set_requirement_date_range" class="form-control " autocomplete="off" value="{{ old('set_requirement_date_range',($old_data['set_requirement_date_range']??''))}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea class='form-control ckeditor' name='description[]'>{{ old('description.'.$loop->index,(($lang == $defaultLanguage)?$old_data['description']: $translate[$lang]['description']))}}</textarea>
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
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/ads/'.$old_data['image'], type: 'backend-product')  }}" alt="">
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
    $(document).ready(function() {
        getTrustList('{{ old("category_id",$old_data["category_id"]) }}');
    });

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
                    $(".fillupdata[data-point='2']").val("{{ old('trust_id',$old_data['trust_id']) }}");
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
</script>
<script>
    let rowCounter = Number("{{ !empty($langData) ? count($langData) - 1 : 0 }}");

    // Function to add a new row
    function addRowBtn() {
        rowCounter++;
        let fullHtml = '';
        <?php foreach ($languages as $k1 => $lang) { ?>
            const newRow<?php echo $lang; ?> = document.createElement('div');
            newRow<?php echo $lang; ?>.className = 'row form-row row_lang_' + rowCounter;
            newRow<?php echo $lang; ?>.id = `row-<?php echo $lang; ?>-${rowCounter}`;
            fullHtml = `
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
            <div class="col-md-2 mt-2 set_amount_display">
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
            <div class="col-md-2 mt-2 set_amount_display">
                <label class="title-color" for="image_product_${rowCounter}">${(("{{$k1}}" == 0)?'Product Image':'Image preview')}"<span class="text-danger">*</span></label>`;
            if ("{{$k1}}" == 0) {
                fullHtml += `<input type="file" name="image_product[${rowCounter}]" class="form-control image-upload" id="image_product_${rowCounter}">`;
            }
            fullHtml += `<div class="image-preview-container image_preview_${rowCounter}"></div>
            </div>
            <div class="col-md-2 mt-2 set_amount_display">
                <label class="w-100">&nbsp;</label>
                 <input type="hidden" name="existing_images[${rowCounter}]" value="null">
                <button type="button" class="btn btn-danger btn-remove-row" onclick="removeRow(${rowCounter})">Remove</button>
            </div>
        `;
            newRow<?php echo $lang; ?>.innerHTML = fullHtml;

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