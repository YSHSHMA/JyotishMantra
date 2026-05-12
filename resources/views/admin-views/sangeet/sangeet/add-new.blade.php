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
         <img width="25" src="{{ asset('public/assets/back-end/img/sangeet/logo.png') }}" alt="">
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
                     <div class="col-md-4">
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
                     <div class="col-md-4">
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
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="title-color" for="language">{{ translate('Select_Language') }}<span class="text-danger">*</span></label>
                           <select name="language" class="form-control" id="language" required>
                              <option value="">{{ translate('Select Language') }}</option>
                              @foreach($sangeetLanguages as $language)
                              <option value="{{ $language->name }}">{{ $language->name }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                  </div>

   <div class="d-flex gap-3 justify-content-end">
       <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
       <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
    </div>
  </form> 
</div>
   </div>
   </div>
   </div>
   </div>
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
});

</script>
@endpush
