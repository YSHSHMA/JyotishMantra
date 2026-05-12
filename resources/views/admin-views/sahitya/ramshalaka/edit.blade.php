@extends('layouts.back-end.app')

@section('title', translate('ram_shalaka'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_ram_shalaka') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.ramshalaka.update', [$ramshalaka['id']]) }}"
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title-color" for="letter">{{ translate('letter') }}</label>
                                    <input type="text" name="letter" value="{{ $ramshalaka['letter'] ?? '' }}" class="form-control" id="letter" placeholder="{{ translate('letter') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title-color" for="chaupai">{{ translate('chaupai') }}</label>
                                    <input name="chaupai" value="{{ $ramshalaka['chaupai'] ?? '' }}" class="form-control" id="chaupai" placeholder="{{ translate('Enter chaupai here') }} ">
                                </div>
                            </div>
                        </div>

                          <div class="row mb-3">
                            <div class="col-md-8">
                                 @foreach ($language as $lang)
                                        <?php
                                        if (count($ramshalaka['translations'])) {
                                            $translate = [];
                                            foreach ($ramshalaka['translations'] as $translations) {
                                                if ($translations->locale == $lang && $translations->key == 'description') {
                                                    $translate[$lang]['description'] = $translations->value;
                                                }
                                                
                                            }
                                        }
                                        ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="title-color" for="description">{{ translate('Description') }} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="description[]"
                                        value="{{ $lang == $defaultLanguage ? $ramshalaka['description'] : $translate[$lang]['description'] ?? '' }}"
                                        class="form-control" id="description"
                                        placeholder="{{ translate('ex') }} : {{ translate('Description') }}"
                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                    </div>
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
