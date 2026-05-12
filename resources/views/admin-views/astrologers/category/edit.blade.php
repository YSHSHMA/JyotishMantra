@extends('layouts.back-end.app')

@section('title', translate('category_Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/category.png') }}" alt="">
                {{ translate('category_Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.astrologers.category.update', [$category['id']]) }}" method="post"
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
                                        if (count($category['translations'])) {
                                            $translate = [];
                                            foreach ($category['translations'] as $translations) {
                                                if ($translations->locale == $lang && $translations->key == 'name') {
                                                    $translate[$lang]['name'] = $translations->value;
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
                                                    value="{{ $lang == $defaultLanguage ? $category['name'] : $translate[$lang]['name'] ?? '' }}"
                                                    class="form-control" id="name"
                                                    placeholder="{{ translate('ex') }} : {{ translate('Name') }}"
                                                    {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    @endforeach

                                    <div class="form-group">
                                        <label for="image" class="title-color">
                                            {{ translate('category_Image') }}<span class="text-danger">*</span>
                                        </label>
                                        <span class="ml-1 text-info">
                                            {{ THEME_RATIO[theme_root_path()]['Category Image'] }}
                                        </span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="category-image"
                                                class="custom-file-input image-preview-before-upload" data-preview="#viewer"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="category-image">
                                                {{ translate('choose_file') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <img class="upload-img-view" id="viewer"
                                            src="{{ getValidImage(path: 'storage/app/public/astrologer-category-img/' . $category['image'], type: 'backend-category') }}"
                                            alt="">
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
@endpush
