@extends('layouts.back-end.app')

@section('title', translate('birth_journal_edit'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            {{ translate('birth_journal_edit') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body text-start">
                    <form action="{{ route('admin.birth_journal.updatesave',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item">
                                <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage ? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ ucfirst(getLanguageName($lang)).'('.strtoupper($lang).')' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('name') }} <span class="text-danger">*</span></label>
                                <select name="name" class="form-control @error('name') is-invalid @enderror">
                                    <option value="kundali" {{ old('name',$getData['name']) == 'kundali' ? 'selected' : '' }}>{{ translate('kundali') }}</option>
                                    <option value="kundali_milan" {{ old('name',$getData['name']) == 'kundali_milan' ? 'selected' : '' }}>{{ translate('kundali_milan') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('Type_of_Kundali') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror">
                                    <option value="basic" {{ old('type',$getData['type']) == 'basic' ? 'selected' : '' }}>{{ translate('basic') }}</option>
                                    <option value="pro" {{ old('type',$getData['type']) == 'pro' ? 'selected' : '' }}>{{ translate('professional') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('Selling_Price') }} <span class="text-danger">*</span></label>
                                <input type="text" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price',$getData['selling_price']) }}" placeholder="{{ translate('Selling_Price') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('pages_no') }} <span class="text-danger">*</span></label>
                                <input type="text" name="pages" class="form-control @error('pages') is-invalid @enderror" value="{{ old('pages',$getData['pages']) }}" placeholder="{{ translate('page_no') }}">
                            </div>
                            <div class="col-md-12">
                                @foreach($language as $lang)
                                <?php
                                            if (count($getData['translations'])) {
                                                $translate = [];
                                                foreach ($getData['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == "short_description") {
                                                        $translate[$lang]['short_description'] = $translations->value;
                                                    }
                                                    if ($translations->locale == $lang && $translations->key == "description") {
                                                        $translate[$lang]['description'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="name" class="title-color"> {{ translate('short_description') }} <span class="text-danger">*</span> ({{strtoupper($lang) }}) </label>
                                            <textarea name="short_description[]" class="form-control ckeditor" placeholder="{{ translate('short_description') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('short_description.'.$loop->index , ($lang == $defaultLanguage ? $getData['short_description']:($translate[$lang]['short_description']??'')) ) }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="name" class="title-color"> {{ translate('description') }} <span class="text-danger">*</span> ({{strtoupper($lang) }}) </label>
                                            <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('description.'.$loop->index ,($lang == $defaultLanguage ? $getData['description']:($translate[$lang]['description']??''))) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach                                
                            </div>
                            <div class="col-md-6 mb-4">
                            <div class="form-group">
                                    <label for="name" class="title-color">
                                        {{ translate('janam_patrika_Image') }}<span class="text-danger">*</span>
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Product Image'] }}
                                    </span>
                                    
                                </div>
                                <div class="text-center">
                                    <input type="file" name="image" id="brand-image" class="custom-file-input image-preview-before-upload" data-preview="#viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" style="width: 167px; height: 100%;   position: absolute;">
                                    <img class="upload-img-view" id="viewer" src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/'.$getData['image'], type: 'backend-brand') }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end"><input type='hidden' name='id' value="{{ $getData['id']}}">
                            <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary px-4">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
@endpush