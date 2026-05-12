@extends('layouts.back-end.app-event')

@section('title', (($type == 'edit') ? translate('Update_Artist') : translate('view_Artist') ) )

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
           {{ (($type == 'edit') ? translate('Update_Artist') : translate('view_Artist') ) }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new artist -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    @if($type == 'edit')
                    <form action="{{ route('event-vendor.artist.update-artist',['id'=>$getData['id']]) }}" method="post" enctype="multipart/form-data">
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
                            <div class="col-md-8">
                                @foreach($languages as $lang)
                                <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'name') {
                                            $translate[$lang]['name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'profession') {
                                            $translate[$lang]['profession'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                    }
                                } ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <label class="title-color" for="name">{{ translate('artist_name') }}<span class="text-danger">*</span>({{ strtoupper($lang) }})</label>
                                    <input type="text" name="name[]" class="form-control" id="name" value="{{ old('name.'.$loop->index,($translate[$lang]['name']??$getData['name']))}}" placeholder="{{ translate('artist_name_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                    <label class="title-color" for="name">{{ translate('profession') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea name="profession[]" class="form-control ckeditor" placeholder="{{ translate('Enter_profession') }}" {{$lang == $defaultLanguage? 'required':''}}>{{ old('profession.'.$loop->index,($translate[$lang]['profession']??$getData['profession']))}}</textarea>
                                    <label class="title-color" for="description">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('Enter_description') }}">{{ old('description.'.$loop->index,($translate[$lang]['description']??$getData['description']))}}</textarea>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <img class="upload-img-view" id="detail-viewer" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['image']??'', type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color">
                                        {{ translate('artist_image') }}<span class="text-danger">*</span>
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons for form actions -->
                         @if (Helpers::Employee_modules_permission('Artist Management', 'Artist Update', 'Update'))
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                        @endif
                    </form>
                    @else
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
                        <div class="col-md-8">
                            @foreach($languages as $lang)
                            <?php
                            $translate = [];
                            if (count($getData['translations'])) {
                                foreach ($getData['translations'] as $translations) {
                                    if ($translations->locale == $lang && $translations->key == 'name') {
                                        $translate[$lang]['name'] = $translations->value;
                                    }
                                    if ($translations->locale == $lang && $translations->key == 'profession') {
                                        $translate[$lang]['profession'] = $translations->value;
                                    }
                                    if ($translations->locale == $lang && $translations->key == 'description') {
                                        $translate[$lang]['description'] = $translations->value;
                                    }
                                }
                            } ?>
                            <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                <div class="col-12">
                                    <label class="title-color font-weight-bolder" for="name">{{ translate('artist_name') }}({{ strtoupper($lang) }})</label>
                                    : {{ ($translate[$lang]['name']??$getData['name']) }}
                                </div>
                                <div class="col-12">
                                    <label class="title-color font-weight-bolder" for="name">{{ translate('profession') }} ({{ strtoupper($lang) }})</label> <br>
                                    {!! ($translate[$lang]['profession']??$getData['profession']) !!}
                                </div>
                                <div class="col-12">
                                    <label class="title-color font-weight-bolder" for="description">{{ translate('description') }} ({{ strtoupper($lang) }})</label> <br>
                                    {!! ($translate[$lang]['description']??$getData['description']) !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <img class="upload-img-view" id="detail-viewer" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['image']??'', type: 'backend-product')  }}" alt="">
                            </div>
                            <div class="form-group">
                                <label for="detail_image" class="title-color">
                                    {{ translate('artist_image') }}<span class="text-danger">*</span>
                                </label>
                                <span class="ml-1 text-info">
                                    {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                </span>

                            </div>
                        </div>
                        <div class="col-md-12">
                                <p>🚫 Only admin can edit, so please raise a support ticket.</p>
                        </div>
                    </div>

                    @endif
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

@endpush