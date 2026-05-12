@extends('layouts.back-end.app')
@section('title', translate('sangeet_Add'))
@section('content')
@push('css_or_js')
{{-- datepicker --}}
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ asset('public/assets/back-end/img/sangeet.png') }}" alt="">
         {{ translate('sangeet_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.sangeet.add-new') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label for="category_id" class="title-color">{{ translate('Sangeet Category') }}<span class="text-danger">*</span></label>
                           <select name="category_id" class="form-control" id="category_id" required>
                              <option value="">{{ translate('Select Category') }}</option>
                              @foreach($sangeetCategories as $category)
                              <option value="{{ $category->id }}">{{ $category->name }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="title-color" for="name">{{ translate('Select_Subcategory') }}<span class="text-danger">*</span></label>
                           <select name="subcategory_id" class="form-control" id="subcategory_id" required>
                              <option value="">{{ translate('Select SubCategory') }}</option>
                              @foreach($sangeetSubCategories as $subcategory)
                              <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="title-color" for="language">{{ translate('Select_Language') }}<span class="text-danger">*</span></label>
                           <select name="language[]" class="form-control" id="language" multiple required>
                              <option value="">{{ translate('Select Language') }}</option>
                              @foreach($sangeetLanguages as $language)
                              <option value="{{ $language->language }}">{{ $language->language }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="title-color" for="name">{{ translate('Select_Singer') }}<span class="text-danger">*</span></label>
                           <select name="singer_id" class="form-control" id="singer_id" required>
                              <option value="">{{ translate('Select Singer') }}</option>
                              @foreach($sangeetSingers as $singer)
                              <option value="{{ $singer->id }}">{{ $singer->singer_name }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                  </div>
                 
<!--                   <div class="d-flex gap-3 justify-content-end">
                     <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                  </div> -->
            </div>
         </div>
      </div>
   </div>

   <!-- Dynamic Fields Container -->
   <div id="dynamic-fields" style="display: none;">
      <div id="new-fields-container"></div>
   </div>
<!-- Template for New Fields -->
<div id="template" style="display: none;">
    <div class="card mb-3 dynamic-card">
        <div class="card-body">
           
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title[]" class="form-control" placeholder="Enter Title" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Singer Name</label>
                        <input type="text" name="singer_name[]" class="form-control" placeholder="Enter Singer Name" required>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="audio" class="title-color">{{ translate('Choose Audio File') }}<span class="text-danger">*</span></label>
                        <input type="file" name="audio[]" class="form-control" accept=".mp3, .wav, .ogg, .aac, .m4a" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="input-label d-flex" for="module_type">{{ translate('lyrics') }}</label>
                        <textarea class="ckeditor form-control" name="lyrics[]" placeholder="Song Lyrics"></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="detail_image" class="title-color">{{ translate('thumbnail') }}<span class="text-danger">*</span></label>
                    <div class="text-center">
                        <img class="upload-img-view" id="detail-viewer-0" src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                    </div><br>
                    <div class="form-group">
                        <div class="custom-file text-left">
                            <input type="file" name="image[]" id="image-0" class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer-0" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="detail-image">{{ translate('choose_file') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="background_image" class="title-color">{{ translate('background') }}<span class="text-danger">*</span></label>
                    <div class="text-center">
                        <img class="upload-img-view" id="background-preview-0" src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                    </div><br>
                    <div class="form-group">
                        <div class="custom-file text-left">
                            <input type="file" name="background_image[]" id="background_image-0" class="custom-file-input image-preview-before-upload" data-preview="#background-preview-0" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="background_image-0">{{ translate('choose_file') }}</label>
                        </div>
                    </div>
                </div>
            </div>
                  <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-danger remove-entry" data-target=".dynamic-card">Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-3 justify-content-end">
       <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
       <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
    </div>
  </form>
</div>
@endsection

@push('script')
{{-- datepicker --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
<script>
// datepicker
$(function() {
    $("#date").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:-0"
    });
});

$(document).ready(function () {
    $('#category_id').change(function () {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: "{{ route('admin.sangeet.subcategories') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id: categoryId
                },
                success: function (response) {
                    $('#subcategory_id').html(response);
                }
            });
        } else {
            $('#subcategory_id').html('<option value="">{{ translate("Select SubCategory") }}</option>');
        }
    });

    // Handle Language Selection and Adding Fields
    $('#language').change(function () {
        // Clear existing fields
        $('#new-fields-container').html('');

        // Show the dynamic fields section
        $('#dynamic-fields').show();

        // Loop through selected languages and add a new card for each
        $(this).find('option:selected').each(function() {
            var template = $('#template').html();
            $('#new-fields-container').append(template);
        });
    });
});

// Remove entry
$(document).on('click', '.remove-entry', function () {
    $(this).closest('.dynamic-card').remove();
});
</script>
@endpush
