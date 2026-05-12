@extends('layouts.back-end.app')

@section('title', translate('birth_journal'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <!-- <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand.png') }}" alt=""> -->
            {{ translate('birth_journal') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body text-start">
                    <form action="{{ route('admin.birth_journal.store') }}" method="post" enctype="multipart/form-data">
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
                                    <option value="kundali" {{ old('name') == 'kundali' ? 'selected' : '' }}>{{ translate('kundali') }}</option>
                                    <option value="kundali_milan" {{ old('name') == 'kundali_milan' ? 'selected' : '' }}>{{ translate('kundali_milan') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('Type_of_Kundali') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror">
                                    <option value="basic" {{ old('type') == 'basic' ? 'selected' : '' }}>{{ translate('basic') }}</option>
                                    <option value="pro" {{ old('type') == 'pro' ? 'selected' : '' }}>{{ translate('professional') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('Selling_Price') }} <span class="text-danger">*</span></label>
                                <input type="text" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" placeholder="{{ translate('Selling_Price') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="title-color"> {{ translate('pages_no') }} <span class="text-danger">*</span></label>
                                <input type="text" name="pages" class="form-control @error('pages') is-invalid @enderror" value="{{ old('pages') }}" placeholder="{{ translate('page_no') }}">
                            </div>

                            <div class="col-md-12">
                                @foreach($language as $lang)
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="name" class="title-color"> {{ translate('short_description') }} <span class="text-danger">*</span> ({{strtoupper($lang) }}) </label>
                                            <textarea name="short_description[]" class="form-control ckeditor" placeholder="{{ translate('short_description') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('short_description.'.$loop->index) ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="name" class="title-color"> {{ translate('description') }} <span class="text-danger">*</span> ({{strtoupper($lang) }}) </label>
                                            <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('description.'.$loop->index) ?? '' }}</textarea>
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
                                    <input type="file" name="image" id="brand-image" class="custom-file-input image-preview-before-upload" data-preview="#viewer" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" style="width: 167px; height: 100%;   position: absolute;">
                                    <img class="upload-img-view" id="viewer"
                                    src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <button type="reset" id="reset"
                                class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
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