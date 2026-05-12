@extends('layouts.back-end.app')

@section('title', translate('calculator_Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/calculator.png') }}" alt="">
                {{ translate('calculator_Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.calculator.update', [$calculator['id']]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($language as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span
                                            class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                            id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-md-8">
                                    @foreach ($language as $lang)
                                        <?php
                                        if (count($calculator['translations'])) {
                                            $translate = [];
                                            foreach ($calculator['translations'] as $translations) {
                                                if ($translations->locale == $lang && $translations->key == 'name') {
                                                    $translate[$lang]['name'] = $translations->value;
                                                }
                                                if ($translations->locale == $lang && $translations->key == 'description') {
                                                    $translate[$lang]['description'] = $translations->value;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                            id="{{ $lang }}-form">
                                            <div class="form-group">
                                                <label class="title-color" for="name">{{ translate('name') }}
                                                    ({{ strtoupper($lang) }})
                                                </label>
                                                <input type="text" name="name[]"
                                                    value="{{ $lang == $defaultLanguage ? $calculator['name'] : $translate[$lang]['name'] ?? '' }}"
                                                    class="form-control" id="name"
                                                    placeholder="{{ translate('ex') }} : {{ translate('Name') }}"
                                                    {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                            </div>
                                            <div class="form-group">
                                                <label class="title-color" for="description">{{ translate('description') }}
                                                    ({{ strtoupper($lang) }})</label>
                                                <textarea name="description[]" class="form-control ckeditor" id="description"
                                                    {{ $lang == $defaultLanguage ? 'required' : '' }}>{!! $translate[$lang]['description']??$calculator['description'] !!}</textarea>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    @endforeach
                                    <div class="form-group">
                                        <label for="url" class="title-color">
                                            {{ translate('video (url)') }}
                                        </label>
                                        <input type="url" id="url" name="url" class="form-control"
                                            value="{{ isset($calculator['url']) ? $calculator['url'] : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <img class="upload-img-view" id="viewer"
                                            src="{{ getValidImage(path: 'storage/app/public/calculator-img/' . $calculator['logo'], type: 'backend-calculator') }}"
                                            alt="">
                                    </div>
                                    <div class="form-group">
                                        <label for="logo" class="title-color">
                                            {{ translate('calculator_Logo') }}
                                        </label>
                                        <span class="ml-1 text-info">
                                            {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                        </span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="logo" id="calculator-image"
                                                class="custom-file-input image-preview-before-upload" data-preview="#viewer"  accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="calculator-image">
                                                {{ translate('choose_file') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <img class="upload-img-view" id="detail-viewer"
                                            src="{{ getValidImage(path: 'storage/app/public/calculator-img/' . $calculator['detail_image'], type: 'backend-calculator') }}"
                                            alt="">
                                    </div>
                                    <div class="form-group">
                                        <label for="detail_image" class="title-color">
                                            {{ translate('detail_Image') }}
                                        </label>
                                        <span class="ml-1 text-info">
                                            {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                        </span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="detail_image" id="detail_image"
                                                class="custom-file-input image-preview-before-upload"
                                                data-preview="#detail-viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="detail-image">
                                                {{ translate('choose_file') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
@endpush
