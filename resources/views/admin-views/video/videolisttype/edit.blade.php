@extends('layouts.back-end.app')

@section('title', translate('video list Type'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/logo.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_video_ListType') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.videolisttype.update', [$videolisttype['id']]) }}"
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
                                $translation = $videolisttype->translations->where('locale', $lang)->first();
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                                    id="{{$lang}}-form">
                                    <label class="title-color" for="name">{{ translate('videolisttype_Name') }}
                                        ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]"
                                        value="{{ $lang == $defaultLanguage ? $videolisttype->name : ($translation ? $translation->value : '') }}"
                                        class="form-control" id="name_{{ $lang }}"
                                        placeholder="{{ translate('enter_videolisttype_Name') }}" {{$lang == $defaultLanguage ? 'required':''}}>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                </div>
                                @endforeach
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

