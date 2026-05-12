@extends('layouts.back-end.app')
@section('title', translate('fast_festival_Edit'))
@section('content')
@push('css_or_js')
{{-- datepicker --}}
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
         {{ translate('fast_festival_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.festival.update',[$festival['id']]) }}) }}" method="post" enctype="multipart/form-data">
                  @csrf
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label for="name" class="title-color">
                                {{ translate('en_description') }}
                            </label>
                            <textarea name="en_description" class="form-control ckeditor" id="title" value=""
                            placeholder="{{ translate('ex') }} : {{translate('en_description') }}">{{$festival['en_description']}}</textarea>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="name" class="title-color">
                                {{ translate('hi_description') }}
                            </label>
                            <textarea name="hi_description" class="form-control ckeditor" id="title" value=""
                            placeholder="{{ translate('ex') }} : {{translate('hi_description') }}">{{$festival['hi_description']}}</textarea>
                        </div>
                        <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="viewer"
                              src="{{ getValidImage(path: 'storage/app/festival-images/'.$festival['image'], type: 'backend-rashi')}}" alt="">
                        </div>
                        <div class="form-group">
                           <label for="name" class="title-color">
                           {{ translate('fast_festival_Image') }}<span class="text-danger">*</span>
                           </label>
                           <span class="ml-1 text-info">
                           {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                           </span>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="festival-image"
                                 class="custom-file-input image-preview-before-upload"
                                 data-preview="#viewer" required
                                 accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="festival-image">
                              {{translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>
                    </div>
                  
                  <div class="d-flex gap-3 justify-content-end">
                     <button type="reset" id="reset"
                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                  </div>
               </form>
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
           dateFormat: 'yy/mm/dd',
           changeMonth: true,
           changeYear: true,
           yearRange: "-100:-0"
       });
   });
</script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush