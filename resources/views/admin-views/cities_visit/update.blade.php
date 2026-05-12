@extends('layouts.back-end.app')

@section('title', translate('Best Time To Visit'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/videosubcategory.png') }}" alt="">
            {{ translate('Best_Time_To_Visite_Edit') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.citie_visit.edit_cities_visit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Language tabs -->


                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="col-12"><?php $translate = []; ?>
                            @foreach($language as $lang)
                            @foreach($list as $vv)
                            <?php if (count($vv['translations'])) {
                                foreach ($vv['translations'] as $t) {
                                    if ($t->locale == $lang && $t->key == "month_name") {
                                        $translate[$lang]['month_name'] = $t->value;
                                    } else if ($t->locale == $lang && $t->key == "season") {
                                        $translate[$lang]['season'] = $t->value;
                                    } else if ($t->locale == $lang && $t->key == "crowd") {
                                        $translate[$lang]['crowd'] = $t->value;
                                    } else if ($t->locale == $lang && $t->key == "weather") {
                                        $translate[$lang]['weather'] = $t->value;
                                    } else if ($t->locale == $lang && $t->key == "sight") {
                                        $translate[$lang]['sight'] = $t->value;
                                    }
                                }
                            } ?>
                            @endforeach
                            <div class="row {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                <div class='col-md-4 form-group'>
                                    <label class="title-color" for="name">{{ translate('month_name') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="hidden" name='id' value="{{ $id }}">
                                    <input type="text" name="month_name[]" class="form-control" value="{{ $lang==$defaultLanguage?$vv->month_name:($translate[$lang]['month_name']??'') }}" autocomplete="off" placeholder="{{ translate('month_name') }}" required="{{ $lang == $defaultLanguage? 'required':''}}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="title-color" for="name">{{ translate('weather') }}<span class="text-danger">*</span>({{ strtoupper($lang) }})</label>
                                    <input type="text" name="weather[]" class="form-control" value="{{ $lang==$defaultLanguage?$vv->weather:($translate[$lang]['weather']??'') }}" autocomplete="off" placeholder="{{ translate('Weather') }}" required="{{ $lang == $defaultLanguage? 'required':''}}">

                                </div>
                                <div class="col-md-4 form-group">

                                    <label class="title-color" for="name">{{ translate('sight') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="sight[]" class="form-control" placeholder="{{ translate('sight') }}" value="{{ $lang==$defaultLanguage?$vv->sight:($translate[$lang]['sight']??'') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('season') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="season[]" class="form-control" placeholder="{{ translate('season') }}" value="{{ $lang==$defaultLanguage?$vv->season:($translate[$lang]['season']??'') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('Crowd') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="crowd[]" class="form-control" placeholder="{{ translate('crowd') }}" value="{{ $lang==$defaultLanguage?$vv->crowd:($translate[$lang]['crowd']??'') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                </div>


                            </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                    <div>
                                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('image') }}</label>
                                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Weather_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="custom_upload_input">
                                                        <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_gst_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                            <i class="tio-delete"></i>
                                                        </span>
                                                        <div class="img_area_with_preview position-absolute z-index-2">
                                                            <img id="pre_gst_img_viewer" class="h-auto aspect-1 bg-white {{ (($list['translation']['image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/cities/visit/'.$list['translation']['image'] ,type: 'backend-product') }}" alt="">
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
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush