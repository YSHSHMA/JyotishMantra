@extends('layouts.back-end.app')
@section('title', translate('Fast Festival Edit'))
@section('content')
@push('css_or_js')
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset('public/assets/back-end/img/festival.png') }}" alt="">
         {{ translate('Fast_Festival_Edit') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.fastfestival.update', $fastfestival->id) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row mb-3">
                     <div class="col-md-6">
                        <label for="hindi_event_name" class="title-color">
                           {{ translate('Hindi Event Name') }}
                           <span class="text-danger">*</span>
                           </label>
                           <input type="text" name="event_name_hi" class="form-control" value="{{ old('event_name_hi', $fastfestival->event_name_hi) }}" required>
                     </div>
                     <div class="col-md-6">
                        <label for="event_type" class="title-color">
                           {{ translate('Event Type') }}
                           <span class="text-danger">*</span>
                           </label>
                           <input type="text" name="event_type" class="form-control" value="{{ old('event_type', $fastfestival->event_type) }}" required>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-8">
                        <div class="form-group">
                           <label for="en_description" class="title-color">
                           {{ translate('En Description') }}
                           <span class="text-danger">*</span>
                           </label>
                           <textarea name="en_description" class="form-control ckeditor" id="en_description" placeholder="{{ translate('ex') }} : {{ translate('Description') }}" required>{{ old('en_description', $fastfestival->en_description) }}</textarea>
                        </div>
                     </div>
                  </div>
                     <div class="row">
                        <div class="col-md-8">
                           <div class="form-group">
                              <label for="hi_description" class="title-color">
                              {{ translate('Hi Description') }}
                              <span class="text-danger">*</span>
                              </label>
                              <textarea name="hi_description" class="form-control ckeditor" id="hi_description" placeholder="{{ translate('ex') }} : {{ translate('Description') }}" required>{{ old('hi_description', $fastfestival->hi_description) }}</textarea>
                           </div>
                        </div>
                        <div class="col-md-4 mb-4">
                           <div class="text-center mb-3">
                               <img class="upload-img-view" id="viewer"
                                             src="{{ getValidImage(path: 'storage/app/public/fastfestival-img/'.$fastfestival['image'], type: 'backend-fastfestival') }}"
                                             alt="">
                           </div>
                           <div class="form-group">
                              <label for="image" class="title-color">
                              {{ translate('Fast_Festival_Image') }}
                              </label>
                              <span class="ml-1 text-info">
                              Ratio 2:1 (600 x 500 px)
                              </span>
                              <div class="custom-file text-left">
                                 <input type="file" name="image" id="fastfestival-image" class="custom-file-input image-preview-before-upload" data-preview="#viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                 <label class="custom-file-label" for="fastfestival-image">
                                 {{ translate('Choose File') }}
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="d-flex gap-3 justify-content-end">
                     <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('Reset') }}</button>
                     <button type="submit" class="btn btn--primary px-4">{{ translate('Submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<!-- <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->
{{--ck editor--}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script>
   initSample();
</script>
<script type="text/javascript">
   $(document).ready(function() {
       $('.ckeditor').ckeditor();
       $('.datepicker').datepicker({
           dateFormat: 'yy-mm-dd'
       });
     
</script>
<script src="{{ dynamicAsset('public/assets/back-end/js/products-management.js') }}"></script>
@endpush
