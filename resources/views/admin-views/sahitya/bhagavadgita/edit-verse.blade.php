@extends('layouts.back-end.app')

@section('title', translate('bhagavad_gita'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_Bhagavad_Gita') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.bhagavadgita.update.details', $bhagavadgitaDetail->id) }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          @method('PUT') 

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
                          <input type="hidden" name="chapter_id" value="{{ $bhagavadgitaDetail->chapter_id }}">

                          <div class="row mb-3">
                            <div class="col-md-8">
                                 @foreach ($language as $lang)
                                        <?php
                                        if (count($bhagavadgitaDetail['translations'])) {
                                            $translate = [];
                                            foreach ($bhagavadgitaDetail['translations'] as $translations) {

                                                if ($translations->locale == $lang && $translations->key == 'description') {
                                                    $translate[$lang]['description'] = $translations->value;
                                                }
                                                
                                            }
                                        }
                                        ?>

                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="form-group">
                                  <label for="chapter_name">Chapter Name</label>
                                  <input type="text" class="form-control" id="chapter_name" name="chapter_name" 
                                         value="{{ $currentChapter ? $currentChapter->name : 'Chapter not found' }}" 
                                         readonly>
                                </div>
                                    <div class="form-group">
                                        <label class="title-color" for="description">{{ translate('description') }}
                                            ({{ strtoupper($lang) }})</label>
                                        <textarea name="description[]" class="form-control ckeditor" id="description"
                                            {{ $lang == $defaultLanguage ? 'required' : '' }}>{!! $translate[$lang]['description']??$bhagavadgitaDetail['description'] !!}</textarea>
                                    </div>
                                     <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="chapter" class="title-color text-capitalize">{{ translate('chapter') }}</label>
                                <div class="form-group">
                                   <input class="form-control" type="text" name="chapter_id" id="chapter-id" value="{{ $bhagavadgitaDetail->chapter_id }}" readonly>
                                </div>

                                <!-- Verse dropdown -->
                                <div id="verse-field" class="form-group">
                                    <label for="verse-select" class="title-color text-capitalize">{{ translate('select_verse') }}</label>
                                    <select id="verse-select" name="verse" class="js-example-responsive form-control w-100 action-display-data">
                                        <option value="">-- Select Verse --</option>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="text-center">
                                    <img class="upload-img-view" id="viewer"
                                        src="{{ getValidImage(path: 'storage/app/public/sahitya/bhagavad-gita/'.$bhagavadgitaDetail->image, type: 'backend-bhagavadgita') }}"
                                        alt="">
                                </div>

                                <div class="form-group">
                                    <label for="image" class="title-color">
                                        {{ translate('Thumbnail') }}
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image"
                                            class="custom-file-input image-preview-before-upload"
                                            data-preview="#viewer"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
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

@push('script')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
   <script>
        // Object with chapters and their respective number of verses
        const chapterVerses = {
            1: 47,
            2: 72,
            3: 43,
            4: 42,
            5: 29,
            6: 47,
            7: 30,
            8: 28,
            9: 34,
            10: 42,
            11: 55,
            12: 20,
            13: 35,
            14: 27,
            15: 20,
            16: 24,
            17: 28,
            18: 78
        };

        // Get the previously selected verse from PHP
        const selectedVerse = "{{ $bhagavadgitaDetail->verse }}"; // Assuming the selected verse is stored in $bhagavadgitaDetail

        // Function to update the verses dropdown based on the selected chapter
        function updateVersesDropdown() {
            const chapterIdInput = document.getElementById('chapter-id');
            const selectedChapter = chapterIdInput.value;
            const verseSelect = document.getElementById('verse-select');
            const verseField = document.getElementById('verse-field');

            // Clear existing options in the verses dropdown
            verseSelect.innerHTML = '<option value="">-- Select Verse --</option>';

            if (selectedChapter && chapterVerses[selectedChapter]) {
                // Get the number of verses for the selected chapter
                const numberOfVerses = chapterVerses[selectedChapter];

                // Populate the verses dropdown
                for (let i = 1; i <= numberOfVerses; i++) {
                    let option = document.createElement('option');
                    option.value = i;
                    option.text = `Verse ${i}`;
                    verseSelect.appendChild(option);
                }

                // Show the verse dropdown
                verseField.style.display = 'block';

                // Set the previously selected verse if it exists
                if (selectedVerse) {
                    verseSelect.value = selectedVerse;
                }
            } else {
                // Hide the verse dropdown if no valid chapter is selected
                verseField.style.display = 'none';
            }
        }

        // Run the function when the page loads to display the correct verses
        window.onload = updateVersesDropdown;
    </script>
@endpush