@extends('layouts.back-end.app')
@section('title', translate('temple_package_master'))
@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
  .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
  top: 14px;
  right: 5px;
  }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
    <h2 class="h1 mb-0 d-flex gap-2">
        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
        {{ translate('temple_package_master') }}
    </h2>
    </div>
   

    <form class="product-form" action="{{ route('admin.temple.storePackage') }}" method="POST">
    @csrf
       <input type="hidden" name="id" value="{{ $editPackage->id ?? '' }}">
    <div class="card">
        <div class="px-4 pt-3">
        <ul class="nav nav-tabs w-fit-content mb-4">
            @foreach ($languages as $lang)
            <li class="nav-item">
            <span
                class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
            </li>
            @endforeach
        </ul>
        </div>
        <div class="card-body">
        @foreach ($languages as $lang)
            <?php
                $translate = [];
                if (!empty($editPackage?->translations)) {
                    foreach ($editPackage->translations as $translation) {
                        if ($translation->locale == $lang && $translation->key == 'name') {
                            $translate[$lang]['name'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'short_description') {
                            $translate[$lang]['short_description'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'type') {
                            $translate[$lang]['type'] = $translation->value;
                        }
                    }
                }
            ?>

    <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
         id="{{ $lang }}-form">
         <input type="hidden" name="lang[]" value="{{ $lang }}">
        <div class="row">
            {{-- Name --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="title-color" for="{{ $lang }}_name">
                        {{ translate('temple_pacakge_name') }} ({{ strtoupper($lang) }})
                    </label>
                    <input type="text" name="name[]"  id="{{ $lang }}_name"  class="form-control" placeholder="{{ translate('temple_pacakge_name') }} ({{ strtoupper($lang) }})"
                           value="{{ $translate[$lang]['name'] ?? $editPackage?->name ?? '' }}" {{ $lang == $defaultLanguage ? 'required' : '' }}>
                </div>
            </div>

            {{-- Short Description --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="title-color" for="{{ $lang }}_short_description">
                        {{ translate('short_description') }} ({{ strtoupper($lang) }})
                    </label>
                    <input type="text"  name="short_description[]"     id="{{ $lang }}_short_description" class="form-control"
                           placeholder="{{ translate('short_description') }} ({{ strtoupper($lang) }})" value="{{ $translate[$lang]['short_description'] ?? $editPackage?->description ?? '' }}"
                           {{ $lang == $defaultLanguage ? 'required' : '' }}>
                </div>
            </div>

            {{-- Type --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="title-color" for="{{ $lang }}_type">
                        {{ translate('type_for_free_and_paid') }} ({{ strtoupper($lang) }})
                    </label>
                    <input type="text" name="type[]" id="{{ $lang }}_type" class="form-control" placeholder="{{ translate('type_for_free_and_paid') }} ({{ strtoupper($lang) }})"
                           value="{{ $translate[$lang]['type'] ?? $editPackage?->type ?? '' }}" {{ $lang == $defaultLanguage ? 'required' : '' }}>
                </div>
            </div>
        </div>
    </div>
@endforeach

        </div>
        <div class="row justify-content-end gap-3 mt-3 mx-1">
        <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
        <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
        </div>
    </div>
    </form>
    <div class="row mt-20">
    <div class="col-md-12">
        <div class="card">
        <div class="px-3 py-4">
            <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                    <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('temple_package_name') }}</th>
                            <th class="max-width-100px">{{ translate('short_description') }}</th>
                            <th class="max-width-100px">{{ translate('type') }}</th>
                            <th class="max-width-100px">{{ translate('status') }}</th>
                            <th class="max-width-100px">{{ translate('Action') }}</th>
                        </tr>
                    <thead>
                    <tbody>
                    @foreach($packages as $key => $package)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $package->name ?? '-' }}</td>
                            <td>{{ $package->description ?? '-' }}</td>
                            <td>{{ $package->type ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.temple.templepackagestatus', ['id' => $package['id']]) }}"        method="post" id="package-status{{$package['id']}}-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$package['id']}}">
                                        <label class="switcher mx-auto">
                                            <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                    id="package-status{{ $package['id'] }}" value="1" {{ $package['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-id = "toggle-status-modal"
                                                    data-toggle-id = "package-status{{ $package['id'] }}"
                                                    data-on-image = "package-status-on.png"
                                                    data-off-image = "package-status-off.png"
                                                    data-on-title = "{{ translate('Want_to_Turn_ON').' '.$package['defaultname'].' '. translate('status') }}"
                                                    data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$package['defaultname'].' '.translate('status') }}"
                                                    data-on-message = "<p>{{ translate('if_enabled_this_package_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message = "<p>{{ translate('if_disabled_this_package_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                            <span class="switcher_control"></span>
                                        </label>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('admin.temple.templepackage', ['id' => $package->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="tio-edit"></i>
                                </a>
                                <a href="{{ route('admin.temple.templepackagedelete', $package->id) }}"
                                    onclick="return confirm('Are you sure you want to delete this package?')"
                                    class="btn btn-sm btn-danger">
                                        <i class="tio-delete"></i>
                                    </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
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
@endpush