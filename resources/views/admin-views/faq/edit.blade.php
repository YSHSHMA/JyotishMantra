@extends('layouts.back-end.app')

@section('title', translate('faq_edit'))

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
            {{ translate('faq_edit') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.faq.update',$faqs['id']) }}" method="post"
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
                if (count($faqs['translations'])) {
                    $translate = [];
                    foreach ($faqs['translations'] as $translation) {
                        if ($translation->locale == $language && $translation->key == "question") {
                            $translate[$language]['question'] = $translation->value;
                        }
                        if ($translation->locale == $language && $translation->key == "detail") {
                            $translate[$language]['detail'] = $translation->value;
                        }
                    }
                }
                ?>
                <div class="{{ $language != 'en'? 'd-none':''}} form-system-language-form" id="{{ $language}}-form">
                    <div class="row">
                        <div class="col-md-6">
                        <label class="title-color">{{ translate('category') }} </label>
                            <select required name="category_id" class="form-control option_category_id" onchange="$('.option_category_id').val(this.value)">
                                <option value="">Select Category</option>
                                @if($category_list)
                                @foreach($category_list as $vv)
                                <option value="{{ $vv['id']}}" {{ ((old('category_id',$faqs['category_id']) == $vv['id'] )?"selected":"" )}}>{{ $vv['name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language}}_name">{{ translate('faq_question') }}
                                    ({{strtoupper($language) }})</label>
                                <input type="text" {{ $language == 'en'? 'required':''}} name="question[]"
                                    id="{{ $language}}_question"
                                    value="{{ $translate[$language]['question']??$faqs['question']}}"
                                    class="form-control" placeholder="{{ translate('new_Product') }}" required>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $language}}">
                        </div>
                    </div>
                    <div class="form-group pt-4">
                        <label class="title-color">{{ translate('faq_answer') }}
                            ({{strtoupper($language) }})</label>
                        <textarea name="detail[]" class="" id="editor{{ $language }}">{!! $translate[$language]['detail']??$faqs['detail'] !!}</textarea>
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