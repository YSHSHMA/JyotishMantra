@extends('layouts.back-end.app')

@section('title', translate('panchangmoonimage'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/panchangmoonimage.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_panchangmoonimage') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.panchangmoonimage.update', [$panchangmoonimage['id']]) }}"
                        method="post" enctype="multipart/form-data">
                        @csrf

                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ getLanguageName($lang).' ('.strtoupper($lang).')' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>

                        <div class="row">
                            <div class="col-md-8">
                                @foreach($language as $lang)
                                <?php
                                $translation = $panchangmoonimage->translations->where('locale', $lang)->first();
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                                    id="{{$lang}}-form">
                                    <label class="title-color" for="name">{{ translate('panchangmoonimage_Name') }}
                                        ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]"
                                        value="{{ $lang == $defaultLanguage ? $panchangmoonimage->name : ($translation ? $translation->value : '') }}"
                                        class="form-control" id="name_{{ $lang }}"
                                        placeholder="{{ translate('enter_panchangmoonimage_Name') }}" {{$lang == $defaultLanguage ? 'required':''}}>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                </div>
                                @endforeach
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <img class="upload-img-view" id="viewer"
                                        src="{{ getValidImage(path: 'storage/app/public/panchang-moon-img/'.$panchangmoonimage->image, type: 'backend-panchang') }}"
                                        alt="">
                                </div>

                                <div class="form-group">
                                    <label for="image" class="title-color">
                                        {{ translate('Thumbnail') }}
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image"
                                            class="custom-file-input image-preview-before-upload"
                                            data-preview="#viewer"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/panchang-moon-image.js') }}"></script>
@endpush
