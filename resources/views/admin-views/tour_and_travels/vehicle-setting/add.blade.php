@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('vehicle_add'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('vehicle_add') }}
        </h2>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tour_vehicle_setting.store') }}" method="post">
                @csrf
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
                    @foreach($languages as $lang)
                    <div class="col-12 form-group {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                        <div class="row">
                            <div class="col-md-6">
                                <label>{{ translate('Enter_type') }}</label>
                                <input type="hidden" name="lang[{{ $lang }}]" value="{{ $lang}}">
                                <input type="text" class="form-control" name="type[{{ $lang }}]" placeholder="{{ translate('Enter_a_type') }}" required>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-11">
                                        <label>{{ translate('Enter_a_vehicle_brand_name') }}</label>
                                        <input type="text" class="form-control" name="brand_name[{{ $lang }}][]" placeholder="{{ translate('Enter_a_vehicle_brand_name') }}" required>
                                    </div>
                                    <div class="col-1 p-0" style="top: 32px;">
                                        <a class="btn btn-sm btn-primary" onclick="addVehicleToAllLanguages()"><i class="tio-add"></i></a>
                                    </div>
                                </div>

                                <div class="vehicle-container" data-lang="{{ $lang }}"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-md-12 mt-2">
                        @if (Helpers::modules_permission_check('Tour', 'Tour Vehicle Type', 'add'))
                        <button type="submit" class="btn btn-primary float-end">{{ translate('save') }}</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
</script>
<script>
    let vehicleIndex = 0;

    function addVehicleToAllLanguages() {
        document.querySelectorAll('.vehicle-container').forEach(function(container) {
            let lang = container.getAttribute('data-lang');
            let html = `
            <div class="vehicle-row row mt-2" data-index="${vehicleIndex}">
                <div class="col-11">
                    <input type="text" class="form-control" name="brand_name[${lang}][]" placeholder="Enter a vehicle brand name" required>
                </div>
                <div class="col-1 p-0">
                    <a class="btn btn-sm btn-danger" onclick="removeVehicle(${vehicleIndex})"><i class="tio-remove"></i></a>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });

        vehicleIndex++; // increase for next item
    }

    function removeVehicle(index) {
        document.querySelectorAll(`.vehicle-row[data-index="${index}"]`).forEach(function(el) {
            el.remove();
        });
    }
</script>



@endpush