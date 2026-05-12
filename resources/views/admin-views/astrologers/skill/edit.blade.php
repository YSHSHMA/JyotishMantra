@extends('layouts.back-end.app')

@section('title', translate('skill_Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/skill.png') }}" alt="">
                {{ translate('skill_Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.astrologers.skill.update', [$skill['id']]) }}" method="post"
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
                                <div class="col-md-12">
                                    @foreach ($language as $lang)
                                        <?php
                                        if (count($skill['translations'])) {
                                            $translate = [];
                                            foreach ($skill['translations'] as $translations) {
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
                                                    value="{{ $lang == $defaultLanguage ? $skill['name'] : $translate[$lang]['name'] ?? '' }}"
                                                    class="form-control" id="name"
                                                    placeholder="{{ translate('ex') }} : {{ translate('Name') }}"
                                                    {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    @endforeach
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
