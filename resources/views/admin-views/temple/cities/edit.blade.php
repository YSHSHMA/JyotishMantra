@extends('layouts.back-end.app')

@section('title', translate('temple_cities_edit'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('temple_cities_edit') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.temple.cities.update',$cities['id']) }}" method="post"
              enctype="multipart/form-data" id="product_form">
            @csrf

            <div class="card">
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach($languages as $language)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab  {{ $language == $defaultLanguage? 'active':''}}" href="#"
                                   id="{{ $language}}-link">{{getLanguageName($language).'('.strtoupper($language).')'}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    @foreach($languages as $language)
                    <?php
                            if (count($cities['translations'])) {
                                $translate = [];
                                foreach ($cities['translations'] as $translation) {
                                    if ($translation->locale == $language && $translation->key == "city") {
                                        $translate[$language]['city'] = $translation->value;
                                        }
                                    if ($translation->locale == $language && $translation->key == "short_desc") {
                                            $translate[$language]['short_desc'] = $translation->value;
                                    }
                                    if ($translation->locale == $language && $translation->key == "famous_for") {
                                            $translate[$language]['famous_for'] = $translation->value;
                                    }
                                    if ($translation->locale == $language && $translation->key == "description") {
                                            $translate[$language]['description'] = $translation->value;
                                    }
                                }
                            }
                        ?>
                        <div class="{{ $language != 'en'? 'd-none':''}} form-system-language-form" id="{{ $language}}-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" for="{{ $language}}_name">{{ translate('cities_name') }}
                                            ({{strtoupper($language) }})</label>
                                            <input class="form-control" name="city[]" value="{{ $translate[$language]['city']??$cities['city']}}"
                                            id="sub-category-select"> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('state_name') }}</label>
                                        <select class="js-select2-custom form-control" name="state_id"
                                            id="">
                                            @foreach ($stateList as $state)
                                                <option value="{{ $state['id'] }}" {{ $cities['state_id'] == $state['id'] ? 'selected' : '' }}> {{ $state['name'] }} </option>
                                            @endforeach
                                        </select>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color" for="{{ $language}}_name">{{ translate('short_descscription') }}
                                            ({{strtoupper($language) }})</label>
                                        <input type="text" {{ $language == 'en'? 'required':''}} name="short_desc[]"
                                            id="{{ $language}}_short_desc"  value="{{ $translate[$language]['short_desc']??$cities['short_desc']}}"
                                            class="form-control" placeholder="{{ translate('short_descscription') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="title-color" for="{{ $language}}_name">{{ translate('famous_for') }}
                                        ({{strtoupper($language) }})</label>
                                    <input type="text" {{ $language == 'en'? 'required':''}} name="famous_for[]"
                                        id="{{ $language}}_famous_for"
                                        value="{{ $translate[$language]['famous_for']??$cities['famous_for']}}"
                                        class="form-control" placeholder="{{ translate('famous_for') }}" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $language}}">
                            <div class="form-group pt-4">
                                <label class="title-color">{{ translate('description') }}
                                    ({{strtoupper($language) }})</label>
                                <textarea name="description[]" class="" id="editor{{ $language }}"
                                >{!! $translate[$language]['description']??$cities['description'] !!}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
           <div class="d-flex justify-content-end gap-3">
                <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
            </div>

        </form>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
   
    {{--ck editor--}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script>
        initSample();
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script type="text/javascript">
    $('.delete_file_input').on('click', function () {
        let $parentDiv = $(this).parent().parent();
        $parentDiv.find('input[type="file"]').val('');
        $parentDiv.find('.img_area_with_preview img').addClass("d-none");
        $(this).removeClass('d-flex');
        $(this).hide();
    });
    </script>
@endpush
