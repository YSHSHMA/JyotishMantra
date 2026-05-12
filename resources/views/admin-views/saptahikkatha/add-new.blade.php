@extends('layouts.back-end.app')
@section('title', translate('saptahik_katha_Add'))
@section('content')
@push('css_or_js')
{{-- datepicker --}}
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/saptahikkatha.png') }}" alt="">
         {{ translate('saptahik_katha_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.saptahikvratkatha.add-new') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  
                  <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>saptahikKatha Title</label>
                                <input type="saptahikkatha" name="title" class="form-control" placeholder="Enter Saptahik Katha Title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{ translate('description')}} </label>
                                <textarea class="ckeditor form-control" name="description" placeholder="Saptahik Katha Detail"></textarea>
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
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:-0"
    });
});
</script>
@endpush