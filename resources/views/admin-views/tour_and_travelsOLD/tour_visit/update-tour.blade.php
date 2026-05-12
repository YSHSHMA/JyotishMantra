@extends('layouts.back-end.app')

@section('title', translate('edit_Tour'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('edit_Tour') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.tour_visits.edit') }}" method="POST" enctype="multipart/form-data" id="services_form">
        @csrf
        <div class="card">
            <input type="hidden" name="id" value="{{ $getData['id'] }}">
            <div class="px-4 pt-3">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    @foreach ($languages as $lang)
                    <li class="nav-item">
                        <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer" id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body">
                @foreach ($languages as $key=>$lang)
                <?php
                $translate = [];
                if (count($getData['translations'])) {
                    foreach ($getData['translations'] as $translations) {
                        if ($translations->locale == $lang && $translations->key == 'tour_name') {
                            $translate[$lang]['tour_name'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'description') {
                            $translate[$lang]['description'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'cities_name') {
                            $translate[$lang]['cities_name'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'country_name') {
                            $translate[$lang]['country_name'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'state_name') {
                            $translate[$lang]['state_name'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'part_located') {
                            $translate[$lang]['part_located'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'highlights') {
                            $translate[$lang]['highlights'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'inclusion') {
                            $translate[$lang]['inclusion'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'exclusion') {
                            $translate[$lang]['exclusion'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'terms_and_conditions') {
                            $translate[$lang]['terms_and_conditions'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'cancellation_policy') {
                            $translate[$lang]['cancellation_policy'] = $translations->value;
                        }
                        if ($translations->locale == $lang && $translations->key == 'notes') {
                            $translate[$lang]['notes'] = $translations->value;
                        }
                    }
                }
                ?>
                <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}tour_name">{{ translate('tour_name') }} ({{ strtoupper($lang) }}) </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="tour_name[]" id="{{ $lang }}tour_name" class="form-control @error('tour_name.'.$loop->index) is-invalid @enderror" value="{{ old('tour_name.'.$loop->index,($lang == $defaultLanguage ? $getData['tour_name'] : $translate[$lang]['tour_name'] ?? '') ) }}" placeholder="{{ translate('tour_name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}tour_name">{{ translate('tour_type') }} </label>
                                <select {{ $lang == $defaultLanguage ? 'required' : '' }} name="tour_type" id="{{ $lang }}tour_type" class="form-control @error('tour_type') is-invalid @enderror tour_types" onchange="$('.tour_types').val(this.value)">
                                    <option value="cities_tour" {{ ((old('tour_type',$getData['tour_type']) == 'cities_tour' )?'selected':'' ) }}>Cities Tour</option>
                                    <option value="special_tour" {{ ((old('tour_type',$getData['tour_type']) == 'special_tour' )?'selected':'' ) }}>special Tour</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_cities_name">{{ translate('cities_name') }} </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="cities_name[]" id="{{ $lang }}_cities_name" class="form-control @error('cities_name.'.$loop->index) is-invalid @enderror getAddress_google" value="{{ old('cities_name.'.$loop->index,($lang == $defaultLanguage ? $getData['cities_name'] : $translate[$lang]['cities_name'] ?? '') ) }}" placeholder="{{ translate('cities_name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_country_name">{{ translate('country_name') }} </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="country_name[]" aria-readonly="readonly" readonly id="{{ $lang }}_country_name" class="form-control @error('country_name.'.$loop->index) is-invalid @enderror " value="{{ old('country_name.'.$loop->index,($lang == $defaultLanguage ? $getData['country_name'] : $translate[$lang]['country_name'] ?? '') ) }}" placeholder="{{ translate('country_name') }}" data-toggle="tooltip" role='tooltip' data-title='Please Select Cities'>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_state_name">{{ translate('state_name') }} </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="state_name[]" aria-readonly="readonly" readonly id="{{ $lang }}_state_name" class="form-control @error('state_name.'.$loop->index) is-invalid @enderror" value="{{ old('state_name.'.$loop->index,($lang == $defaultLanguage ? $getData['state_name'] : $translate[$lang]['state_name'] ?? '') ) }}" placeholder="{{ translate('state_name') }}" data-toggle="tooltip" role='tooltip' data-title='Please Select Cities'>
                                <input type="hidden" name='lat' class="lat_location" value="{{ old('lat', $getData['lat']) }}">
                                <input type="hidden" name='long' class="long_location" value="{{ old('long', $getData['long']) }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-none">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_part_located">{{ 'In which part is it located' }} </label>
                                <input type="text" name="part_located[]" id="{{ $lang }}_part_located" class="form-control @error('part_located.'.$loop->index) is-invalid @enderror " value="{{ old('part_located.'.$loop->index,($lang == $defaultLanguage ? $getData['part_located'] : $translate[$lang]['part_located'] ?? '') ) }}" placeholder="{{ translate('In which part is it located') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_description">{{ translate('description') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="description[]" id="{{ $lang }}_description" class="form-control ckeditor @error('description.'.$loop->index) is-invalid @enderror">{{ old('description.'.$loop->index,($lang == $defaultLanguage ? $getData['description'] : $translate[$lang]['description'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_highlights">{{ translate('highlights') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="highlights[]" id="{{ $lang }}_highlights" class="form-control ckeditor @error('highlights.'.$loop->index) is-invalid @enderror">{{ old('highlights.'.$loop->index,($lang == $defaultLanguage ? $getData['highlights'] : $translate[$lang]['highlights'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_Inclusion">{{ translate('Inclusion') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="inclusion[]" id="{{ $lang }}_Inclusion" class="form-control ckeditor @error('inclusion.'.$loop->index) is-invalid @enderror">{{ old('inclusion.'.$loop->index,($lang == $defaultLanguage ? $getData['inclusion'] : $translate[$lang]['inclusion'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_exclusion">{{ translate('exclusion') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="exclusion[]" id="{{ $lang }}_exclusion" class="form-control ckeditor @error('exclusion.'.$loop->index) is-invalid @enderror">{{ old('exclusion.'.$loop->index,($lang == $defaultLanguage ? $getData['exclusion'] : $translate[$lang]['exclusion'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_terms_and_conditions">{{ translate('terms_and_conditions') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="terms_and_conditions[]" id="{{ $lang }}_terms_and_conditions" class="form-control ckeditor @error('terms_and_conditions.'.$loop->index) is-invalid @enderror">{{ old('terms_and_conditions.'.$loop->index,($lang == $defaultLanguage ? $getData['terms_and_conditions'] : $translate[$lang]['terms_and_conditions'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_cancellation_policy">{{ translate('cancellation_policy ') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="cancellation_policy[]" id="{{ $lang }}_cancellation_policy" class="form-control ckeditor @error('cancellation_policy.'.$loop->index) is-invalid @enderror">{{ old('cancellation_policy.'.$loop->index,($lang == $defaultLanguage ? $getData['cancellation_policy'] : $translate[$lang]['cancellation_policy'] ?? '')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_notes">{{ translate('notes ') }} ({{ strtoupper($lang) }}) </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="notes[]" id="{{ $lang }}_notes" class="form-control ckeditor @error('notes.'.$loop->index) is-invalid @enderror">{{ old('notes.'.$loop->index,($lang == $defaultLanguage ? $getData['notes'] : $translate[$lang]['notes'] ?? '')) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                </div>
                @endforeach
                <div class="row">
                    <div class='col-md-12 form-group'>
                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('Package') }}</label>
                    </div>
                    <div class='col-md-12 form-group add_new_packages'>
                        <div class="row">
                            <div class='col-3'>
                                <label class="title-color fw-bolder" for="Language">{{ translate('cab_name') }}</label>
                            </div>
                            <div class='col-3'>
                                <label class="title-color fw-bolder" for="Language">{{ translate('package_name') }}</label>
                            </div>
                            <div class='col-3'>
                                <label class="title-color fw-bolder" for="Language">{{ translate('max_people') }}</label>
                            </div>
                            <div class="col-3">
                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                            </div>
                        </div>
                        <input type="hidden" id="total_rows" name="total_rows" value="{{ old('total_rows', count(json_decode($getData['package_list'], true))) }}">

                        @php
                        $totalRows = old('total_rows', count(json_decode($getData['package_list'], true))); // dynamic row count
                        $oldData = json_decode($getData['package_list'], true); // retrieve old data if any
                        @endphp

                        @for ($i = 0; $i < $totalRows; $i++)
                            <div class="row mt-2">
                            <div class='col-3 p-0 pr-1'>
                                <select class="form-control point_trigger16{{$i}}" name="cab_id[{{ $i }}]" onchange="select_value(this)" data-point='point_trigger16{{$i}}'>
                                    <option value="" selected disabled>{{ translate('Select_cab') }}</option>
                                    @if($cab_list)
                                    @foreach($cab_list as $cabs)
                                    <option value="{{ $cabs['id'] }}" {{ (collect(old('cab_id.' . $i, $oldData[$i]['cab_id'] ?? ''))->contains($cabs['id'])) ? 'selected' : '' }}>{{ $cabs['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class='col-3 p-0 pr-1'>
                                <select class="form-control select2-multiple point_trigger26{{$i}}" name="package_id[{{ $i }}][]" multiple onchange="select_value(this)" data-point='point_trigger26{{$i}}'>
                                    @if($package_list)
                                    @foreach($package_list as $packval)
                                    <option value="{{ $packval['id'] }}" {{ (collect(old('package_id.' . $i, $oldData[$i]['package_id'] ?? []))->contains($packval['id'])) ? 'selected' : '' }}>{{ $packval['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class='col-3 p-0 pr-1'>
                                <input type='text' class="form-control people_no point_trigger36{{$i}}" name="people[{{ $i }}]" value="{{ old('people.' . $i, $oldData[$i]['people'] ?? '') }}" onkeyup="select_value(this)" data-point='point_trigger36{{$i}}' placeholder="{{ translate('enter_max_people') }}">
                            </div>

                            <div class="col-2 p-0 pr-1">
                                <input type='text' class="form-control price_no point_trigger46{{$i}}" name="price[{{ $i }}]" value="{{ old('price.' . $i, $oldData[$i]['price'] ?? '') }}" onkeyup="select_value(this)" data-point='point_trigger46{{$i}}' placeholder="{{ translate('enter_Price') }}">
                            </div>

                            @if($i == 0)
                            <div class="col-1 p-0">
                                <a class='btn btn--primary btn-sm p-1 mt-2' onclick="add_new_html()"><i class='tio-add'></i></a>
                            </div>
                            @else
                            <div class="col-1 p-0">
                                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                            </div>
                            @endif
                    </div>
                    @endfor
                </div>


                <div class='col-md-3 form-group'>
                    <label class="title-color font-weight-bold h3" for="Language">{{ translate('date_choose') }}</label>
                    <select class="form-control" name='use_date' onchange="((this.value == 0)?$('.use_interested_and_not').addClass('d-none'):$('.use_interested_and_not').removeClass('d-none'))">
                        <option value="0" {{((old('use_date',$getData['use_date']) == 0)?'selected':'' )}}>Not use Date</option>
                        <option value="1" {{((old('use_date',$getData['use_date']) == 1)?'selected':'' )}}>use Date</option>
                    </select>
                </div>
                <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} use_interested_and_not">
                    <label class="title-color font-weight-bold h3" for="Language">{{ translate('start_date_and_end_date') }}</label>
                    <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('startandend_date',($getData['startandend_date']??''))}}" name='startandend_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                </div>
                <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} use_interested_and_not">
                    <label class="title-color font-weight-bold h3" for="Language">{{ translate('pickup_time') }}</label>
                    <input type="text" class="form-control pickup_times" value="{{ old('pickup_time',($getData['pickup_time']??'')) }}" name='pickup_time' placeholder="{{ translate('pickup_time') }}" readonly>
                </div>
                <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} use_interested_and_not">
                    <label class="title-color font-weight-bold h3" for="Language">{{ translate('pickup_location') }}</label>
                    <input type="text" class="form-control pickup_location_get" value="{{ old('pickup_location',($getData['pickup_location']??'')) }}" name='pickup_location' placeholder="{{ translate('pickup_location') }}">
                    <input type="hidden" class="pick_up_lat_location" name='pickup_lat' value="{{ old('pickup_lat',($getData['pickup_lat']??'')) }}">
                    <input type="hidden" class="pick_up_long_location" name='pickup_long' value="{{ old('pickup_long',($getData['pickup_long']??'')) }}">
                </div>
            </div>
        </div>
</div>
<div class="mt-3 rest-part">
    <div class="row g-2">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('tour_image') }}</label>
                                <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_Tour_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="custom_upload_input">
                                <input type="file" name="tour_image" multiple class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_tour_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                    <i class="tio-delete"></i>
                                </span>
                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="pre_tour_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getData['tour_image'], type: 'backend-product') }}" alt="">
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
                    <p class="text-muted">{{ translate('upload_additional_images') }}</p>
                    <div class="coba-area">

                        <div class="row g-2" id="additional_Image_Section">

                            @if (!empty($getData['image']) && json_decode($getData['image'],true))
                            @foreach (json_decode($getData['image'],true) as $key => $photo)
                            @php($unique_id = rand(1111, 9999))

                            <div class="col-sm-12 col-md-4">
                                <div
                                    class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                    <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                        href="{{ route('admin.tour_visits.delete-image', ['id' => $getData['id'], 'name' => $photo]) }}">
                                        <i class="tio-delete"></i>
                                    </a>
                                    <div
                                        class="img_area_with_preview position-absolute z-index-2 border-0">
                                        <img id="additional_Image_{{ $unique_id }}"
                                            alt=""
                                            class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                            src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $photo, type: 'backend-product') }}">
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
    </div>
</div>




<div class="row justify-content-end gap-3 mt-3 mx-1">
    <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
    <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
</div>
</form>
</div>

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
{{-- ck editor --}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

<script>
    // time picker
    $('#opentime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    $('#closetime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });

    initSample();
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
</script>
<script type="text/javascript">
    $('.onfillup').on('input', function() {
        let val = $(this).val();
        let point = $(this).data('point');
        $(`.onfillup[data-point="${point}"]`).val(val);
    });


    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "Select Package",
            allowClear: true
        });
    });
    let pointCounter = 1;

    // function add_new_html() {
    //     var newRow = `
    //             <div class="row mt-2">
    //             <div class='col-3 p-0 pr-1'>
    //                                     <select class="form-control point_trigger1${pointCounter}" name="cab_id[]"  onchange="select_value(this)" data-point='point_trigger1${pointCounter}'>
    //                                         <option value="" selected disabled>{{ translate('Select_cab') }}</option>
    //                                         @if($cab_list)
    //                                         @foreach($cab_list as $cabs)
    //                                         <option value="{{ $cabs['id']}}">{{ $cabs['name']}}</option>
    //                                         @endforeach
    //                                         @endif
    //                                     </select>
    //                                 </div>
    //                 <div class='col-3 p-0 pr-1'>
    //                     <select class="form-control select2-multiple point_trigger2${pointCounter}" multiple name="package_id[${pointCounter}][]"  onchange="select_value(this)" data-point='point_trigger2${pointCounter}'>
    //                                         @if($package_list)
    //                                             @foreach($package_list as $packval)
    //                                                 <option value="{{ $packval['id']}}">{{ $packval['name']}}</option>
    //                                             @endforeach
    //                                         @endif
    //                     </select>
    //                 </div>
    //                 <div class='col-3 p-0 pr-1'>
    //                     <input type='text' class="form-control people_no point_trigger3${pointCounter}" name="people[]" onkeyup="select_value(this)" data-point='point_trigger3${pointCounter}' placeholder="Enter max people">
    //                 </div>
    //                 <div class="col-2 p-0 pr-1">
    //                     <input type='text' class="form-control price_no point_trigger4${pointCounter}" name="price[]" onkeyup="select_value(this)" data-point='point_trigger4${pointCounter}' placeholder="Enter Price">
    //                 </div>
    //                 <div class="col-1 p-0">
    //                     <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
    //                 </div>
    //             </div>
    //         `;
    //     $('.add_new_packages').append(newRow);
    //     $('.select2-multiple').select2({
    //         placeholder: "Select Package",
    //         allowClear: true
    //     });
    // }


    function add_new_html() {
        var totalRows = parseInt(document.getElementById('total_rows').value) + 1;
        document.getElementById('total_rows').value = totalRows;

        var newRow = `
        <div class="row mt-2">
            <div class='col-3 p-0 pr-1'>
                <select class="form-control point_trigger1${totalRows}" name="cab_id[${totalRows}]" onchange="select_value(this)" data-point='point_trigger1${totalRows}'>
                    <option value="" selected disabled>{{ translate('Select_cab') }}</option>
                    @foreach($cab_list as $cabs)
                    <option value="{{ $cabs['id'] }}">{{ $cabs['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class='col-3 p-0 pr-1'>
                <select class="form-control select2-multiple point_trigger2${totalRows}" name="package_id[${totalRows}][]" multiple onchange="select_value(this)" data-point='point_trigger2${totalRows}'>
                    @foreach($package_list as $packval)
                    <option value="{{ $packval['id'] }}">{{ $packval['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class='col-3 p-0 pr-1'>
                <input type='text' class="form-control people_no point_trigger3${totalRows}" name="people[${totalRows}]" value="" onkeyup="select_value(this)" data-point='point_trigger3${totalRows}' placeholder="{{ translate('enter_max_people') }}">
            </div>
            <div class="col-2 p-0 pr-1">
                <input type='text' class="form-control price_no point_trigger4${totalRows}" name="price[${totalRows}]" value="" onkeyup="select_value(this)" data-point='point_trigger4${totalRows}' placeholder="{{ translate('enter_Price') }}">
            </div>
            <div class="col-1 p-0">
                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
            </div>
        </div>
    `;
        // document.querySelector('.add_new_packages').insertAdjacentHTML('beforeend', newRow);
        $('.add_new_packages').append(newRow);
        $('.select2-multiple').select2({
            placeholder: "Select Package",
            allowClear: true
        });
    }


    function remove_html(that) {
        $(that).closest('.row').remove();
    }

    function select_value(that) {
        var point = $(that).data('point');
        $(`.${point}`).val($(`.${point}`).val());
    }
    initializeDateRangePicker(false)

    function initializeDateRangePicker(isSingleDate) {
        var initialDateRange = "{{ old('startandend_date',($getData['startandend_date']??''))}}";
        var startDate, endDate;
        if (initialDateRange) {
            var dates = initialDateRange.split(' - ');
            startDate = moment(dates[0], 'YYYY-MM-DD');
            endDate = moment(dates[1], 'YYYY-MM-DD');
        } else {
            startDate = moment().startOf('day');
            endDate = moment().endOf('day');
        }
        $('.start_date_end_date').daterangepicker({
            singleDatePicker: isSingleDate,
            startDate: startDate,
            endDate: endDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: start.format('YYYY-MM-DD'),
                maxDate: end.format('YYYY-MM-DD')
            });
        });
        if (initialDateRange && initialDateRange.includes(' - ')) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: startDate.format('YYYY-MM-DD'),
                maxDate: endDate.format('YYYY-MM-DD')
            });
        }
    }

    $('.pickup_times').timepicker({
        uiLibrary: 'bootstrap4',
        format: 'hh:MM TT', // Correct format for time display (12-hour with AM/PM)
        modal: true,
        footer: true
    });

    $(".getAddress_google").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment'],
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            let addressComponents = place.address_components;
            let city = '';
            let state = '';
            let country = '';
            let partOfCity = '';
            let neighborhood = '';
            console.log(addressComponents);
            addressComponents.forEach(component => {
                const types = component.types;
                if (types.includes('locality')) {
                    city = component.long_name;
                }
                if (types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (types.includes('country')) {
                    country = component.long_name;
                }
                if (types.includes('sublocality_level_1')) {
                    partOfCity = component.long_name; // Sub-locality or area within the city
                }
                if (types.includes('neighborhood')) {
                    neighborhood = component.long_name; // Neighborhood name, if available
                }
            });
            $("#en_state_name").val(state);
            $("#en_country_name").val(country);
            $("#en_cities_name").val(city);
            $(".lat_location").val(lat);
            $(".long_location").val(lng);
            var points = $(inputElement).data('point');
            getHindiAddress(lat, lng, points, inputElement);
        });
    });

    function getHindiAddress(lat, lng, points, inputElement) {
        const apiKey = '{{$googleMapsApiKey}}';
        const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&language=hi&key=${apiKey}`;

        $.getJSON(geocodeUrl, function(data) {
            if (data.status === 'OK' && data.results.length > 0) {
                let fullAddress = '';
                let city = '';
                let state = '';
                let country = '';
                let streetNumber = '';
                let streetName = '';
                console.log(data.results);

                data.results[0].address_components.forEach(function(component) {
                    const componentType = component.types[0];
                    switch (componentType) {
                        case 'street_number':
                            streetNumber = component.long_name; // Extract street number
                            break;
                        case 'route':
                            streetName = component.long_name; // Extract street name
                            break;
                        case 'locality':
                        case 'sublocality':
                            city = component.long_name; // Extract city name
                            break;
                        case 'administrative_area_level_1':
                            state = component.long_name; // Extract state name
                            break;
                        case 'country':
                            country = component.long_name; // Extract country name
                            break;
                    }
                });

                // Construct the full address in Hindi
                fullAddress = [streetNumber, streetName, city, state, country].filter(Boolean).join(', ');


                $("#in_state_name").val(state);
                $("#in_country_name").val(country);
                $("#in_cities_name").val(city);
            } else {
                console.error('Geocoding API error:', data.status);
            }
        });
    }

    $(".pickup_location_get").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment'],
        });
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            $(".pick_up_lat_location").val(lat);
            $(".pick_up_long_location").val(lng);
        });
    });
</script>


@endpush