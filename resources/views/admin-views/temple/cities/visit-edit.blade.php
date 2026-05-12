@extends('layouts.back-end.app')

@section('title', translate('visite_edit'))

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
                {{ translate('visite_edit') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.visit.update', $visit['id']) }}" method="post"
            enctype="multipart/form-data" id="product_form">
            @csrf
            <input type="hidden" name="citie_id" value="{{ $visit['citie_id'] }}">
            <div class="card">
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $language)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab  {{ $language == $defaultLanguage ? 'active' : '' }}"
                                    href="#"
                                    id="{{ $language }}-link">{{ getLanguageName($language) . '(' . strtoupper($language) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    @foreach ($languages as $language)
                        <?php
                        if (count($visit['translations'])) {
                            $translate = [];
                            foreach ($visit['translations'] as $translation) {
                                if ($translation->locale == $language && $translation->key == 'month_name') {
                                    $translate[$language]['month_name'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'season') {
                                    $translate[$language]['season'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'crowd') {
                                    $translate[$language]['crowd'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'weather') {
                                    $translate[$language]['weather'] = $translation->value;
                                }
                                if ($translation->locale == $language && $translation->key == 'sight') {
                                    $translate[$language]['sight'] = $translation->value;
                                }
                            }
                        }
                        ?>
                        <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form"
                            id="{{ $language }}-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name"
                                            for="{{ $language }}_month_name">{{ translate('month_name') }}
                                            ({{ strtoupper($language) }})
                                        </label>
                                        <input class="form-control" name="month_name[]"
                                            value="{{ $translate[$language]['month_name'] ?? $visit['month_name'] }}"
                                            id="month_name">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $language }}_name">{{ translate('weather') }}
                                            ({{ strtoupper($language) }})</label>
                                        <input type="text" {{ $language == 'en' ? 'required' : '' }} name="weather[]"
                                            id="{{ $language }}_weather"
                                            value="{{ $translate[$language]['weather'] ?? $visit['weather'] }}"
                                            class="form-control" placeholder="{{ translate('weather') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $language }}_sight">{{ translate('sight') }}
                                            ({{ strtoupper($language) }})</label>
                                        <input type="text" {{ $language == 'en' ? 'required' : '' }} name="sight[]"
                                            id="{{ $language }}_sight"
                                            value="{{ $translate[$language]['sight'] ?? $visit['sight'] }}"
                                            class="form-control" placeholder="{{ translate('sight') }}" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $language }}">

                        </div>
                    @endforeach
                    @php
                        $selectedSeason = isset($visit['season']) ? $visit['season'] : '';
                        $selectedCrowd = isset($visit['crowd']) ? $visit['crowd'] : '';
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" for="{{ $language }}_season">{{ translate('season') }}</label>
                                <select class="form-control" name="season">
                                    <option value="0" disabled <?= $selectedSeason == '' ? 'selected' : '' ?>>---<?= translate('select') ?>---</option>
                                    <option value="peak season" <?= $selectedSeason == 'peak season' ? 'selected' : '' ?>>Peak Season</option>
                                    <option value="moderate season" <?= $selectedSeason == 'moderate season' ? 'selected' : '' ?>>Moderate Season</option>
                                    <option value="off-season" <?= $selectedSeason == 'off-season' ? 'selected' : '' ?>>Off-season</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $language }}_crowd">{{ translate('crowd') }}</label>
                                <select class="form-control" name="crowd">
                                    <option value="0"  disabled <?= $selectedCrowd == '' ? 'selected' : '' ?>>---{{ translate('select') }}---</option>
                                    <option value="more crowd" <?= $selectedCrowd == 'more crowd' ? 'selected' : '' ?>>More crowd</option>
                                    <option value="average crowd" <?= $selectedCrowd == 'average crowd' ? 'selected' : '' ?>>Average Crowd</option>
                                    <option value="less-crowd" <?= $selectedCrowd == 'less-crowd' ? 'selected' : '' ?>>Less Crowd</option>
                                </select>
                            </div>
                        </div>
                    </div>
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
