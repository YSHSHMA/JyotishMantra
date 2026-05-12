@extends('layouts.back-end.app')
@section('title', translate('sangeet_Update'))
@section('content')
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 align-items-center d-flex gap-2">
         <img width="22" src="{{ dynamicAsset('public/assets/back-end/img/sangeet/logo.png') }}" alt="">
         {{ translate('sangeet_Update') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body text-start">
               <form action="{{ route('admin.sangeet.update', [$sangeet['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="category_id" class="title-color">
                              {{ translate('Sangeet Category') }}
                              <span class="text-danger">*</span>
                           </label>
                           <select name="category_id" class="form-control" id="category_id" required>
                              <option value="">{{ translate('Select Category') }}</option>
                              @forelse($sangeetCategories as $category)
                              <option value="{{ $category->id }}" {{ $category->id == $sangeet->category_id ? 'selected' : '' }}>
                                 {{ $category->name }}
                              </option>
                              @empty
                              <option value="">{{ translate('No Categories Available') }}</option>
                              @endforelse
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="title-color" for="subcategory_id">{{ translate('Subcategory_Name') }}<span class="text-danger">*</span></label>
                           <select name="subcategory_id" class="form-control" id="subcategory_id" required>
                              <option value="">{{ translate('Select SubCategory') }}</option>
                              @foreach($sangeetSubCategories as $subcategory)
                              <option value="{{ $subcategory->id }}" {{ $subcategory->id == $sangeet->subcategory_id ? 'selected' : '' }}>
                                 {{ $subcategory->name }}
                              </option>
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
                              <option value="{{ $language->name }}" {{ $language->name == $sangeet->language ? 'selected' : '' }}>
                                 {{ $language->name }}
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="text-end">
                     <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
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
