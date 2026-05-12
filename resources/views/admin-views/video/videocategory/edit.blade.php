@extends('layouts.back-end.app')

@section('title', translate('videocategory'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/logo.png') }}" class="mb-1 mr-1" alt="">
                {{ translate('update_videocategory') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12 mb-10">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{route('admin.videocategory.update', [$videocategory['id']])}}" method="post">
                            @csrf

                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($language as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                            {{ getLanguageName($lang).' ('.strtoupper($lang).')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @foreach($language as $lang)
                                    <?php
                                    if (count($videocategory['translations'])) {
                                        $translate = [];
                                        foreach ($videocategory['translations'] as $translations) {
                                            if ($translations->locale == $lang && $translations->key == "name") {
                                                $translate[$lang]['name'] = $translations->value;
                                            }
                                        }
                                    }
                                    ?>
                                <div
                                    class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                                    id="{{$lang}}-form">
                                    <input type="hidden" id="id">
                                    <label class="title-color" for="name">{{ translate('videocategory_Name') }}
                                        ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]"
                                           value="{{$lang==$defaultLanguage?$videocategory['name']:($translate[$lang]['name']??'') }}"
                                           class="form-control" id="name"
                                           placeholder="{{ translate('enter_Videocategory_Name') }}" {{$lang == $defaultLanguage ? 'required':''}}>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                            @endforeach
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" class="btn px-4 btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn px-4 btn--primary">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/videos-management.js') }}"></script>
@endpush
