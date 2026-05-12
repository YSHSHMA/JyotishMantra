@extends('layouts.back-end.app')

@section('title', translate('bhagavad_gita'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('bhagavad_Gita_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new bhagavadgita -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.bhagavadgita.storeDetails') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <!-- Language tabs -->
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($language as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                    <input type="hidden" name="chapter_id" value="{{$id}}">
                     <div class="col-md-8">
                        <div class="form-group">
                          <label for="chapter_name">Chapter Name</label>
                          <input type="text" class="form-control" id="chapter_name" name="chapter_name" 
                                 value="{{ $currentChapter ? $currentChapter->name : 'Chapter not found' }}" 
                                 readonly>
                        </div>

                        <!-- Input fields for bhagavadgita name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <div class="form-group">
                               <label for="name" class="title-color">
                                   {{ translate('description') }}
                                   <span class="text-danger">*</span>
                                   ({{ strtoupper($lang) }})
                               </label>
                               <textarea name="description[]" class="form-control ckeditor" id="description" value=""
                                   placeholder="{{ translate('ex') }} : {{ translate('description') }}" {{ $lang == $defaultLanguage ? '': '' }}></textarea>
                           </div>
                       <input type="hidden" name="lang[]" value="{{ $lang }}">
                        </div>
                        @endforeach
                     </div>

                     <div class="col-md-4 mb-4">
                      <label for="chapter" class="title-color text-capitalize">{{ translate('chapter') }}</label>
                        <div class="form-group">
                           <input class="form-control" type="text" name="chapter_id" id="chapter-id" value="{{$id}}" readonly>
                        </div>

                        <!-- Verse dropdown -->
                        <div id="verse-field" class="form-group" style="display: none;">
                           <label for="verse-select" class="title-color text-capitalize">{{ translate('select_verse') }}</label>
                           <select id="verse-select" name="verse" class="js-example-responsive form-control w-100 action-display-data">
                              <option value="">-- Select Verse --</option>
                              <!-- Options will be populated dynamically -->
                           </select>
                        </div>
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                              alt="">
                        </div>
                        <div class="form-group">
                           <label for="detail_image" class="title-color">
                              {{ translate('thumbnail') }}<span class="text-danger">*</span>
                           </label>
                           <span class="ml-1 text-info">
                              {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                           </span>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="image"
                                 class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer"
                                 accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
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
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

 {{-- datepicker --}}
 <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
 <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
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
      } else {
         // Hide the verse dropdown if no valid chapter is selected
         verseField.style.display = 'none';
      }
   }

   // Run the function when the page loads to display the correct verses
   window.onload = updateVersesDropdown;
</script>

 <script>
    function updateChapterName() {
        const chapterSelect = document.getElementById('chapter-select');
        const chapterNameDisplay = document.getElementById('chapter-name-display');
        const selectedChapterName = chapterSelect.options[chapterSelect.selectedIndex].text;

        // Update the chapter name display
        chapterNameDisplay.textContent = selectedChapterName;

        // Optionally, set the hidden input value
        document.getElementById('chapter-name').value = selectedChapterName;
    }
</script>

@endpush