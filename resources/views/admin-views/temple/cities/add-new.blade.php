@extends('layouts.back-end.app')

@section('title', translate('add_temple_cities'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('add_temple_cities') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.temple.cities.add-new') }}" method="POST"
            enctype="multipart/form-data" id="services_form">
            @csrf
            <div class="card">
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $lang)
                            <li class="nav-item">
                                <span
                                    class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                    id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    
                    @foreach ($languages as $lang)
                        <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $lang }}-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label  class="title-color"for="{{ $lang }}_cities">{{ translate('citie Name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="city[]" id="{{ $lang }}_cities"
                                            class="form-control" placeholder="{{ translate('citie Name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ translate('state_name') }}</label>
                                        <select class="js-select2-custom form-control" name="state_id"
                                            id="">
                                            @foreach ($stateList as $state)
                                                <option value="{{ $state['id'] }}"> {{ $state['name'] }} </option>
                                            @endforeach
                                        </select>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label  class="title-color"for="{{ $lang }}_short_descscription">{{ translate('short_descscription') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="short_desc[]" id="{{ $lang }}_short_desc"
                                            class="form-control" placeholder="short descscription">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label   class="title-color"for="{{ $lang }}_famous_for">{{ translate('famous_for') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }}   name="famous_for[]" id="{{ $lang }}_famous_for" class="form-control"
                                            placeholder="famous for">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                            <label class="title-color" for="{{ $lang }}_description">{{ translate('description') }}
                                ({{ strtoupper($lang) }})</label>
                                <textarea class="ckeditor" id="editor{{ $lang }}" name="description[]" required></textarea>


                        </div>
                    @endforeach


                </div>
            </div>

            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
            </div>
        </form>
    </div>


    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script>
        initSample();
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script type="text/javascript">
        $('.delete_file_input').on('click', function() {
            let $parentDiv = $(this).parent().parent();
            $parentDiv.find('input[type="file"]').val('');
            $parentDiv.find('.img_area_with_preview img').addClass("d-none");
            $(this).removeClass('d-flex');
            $(this).hide();
        });
    </script>
@endpush
