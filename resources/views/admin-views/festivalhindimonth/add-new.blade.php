@extends('layouts.back-end.app')
@section('title', translate('festival_hindi_month_Add'))
@section('content')
@push('css_or_js')
{{-- datepicker --}}
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festivalhindimonth.png') }}" alt="">
         {{ translate('festival_hindi_month_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.festivalhindimonth.add-new') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  
                  <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Festival Hindi Month Title</label>
                                <input type="text" name="festival_hindimonth_title" class="form-control" placeholder="Enter festival Hindi Month Title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hindi Month Year</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    @php
                                    $currentYear = date("Y");
                                    @endphp
                                    @for ($i = 2060; $i >= 1930; $i--)
                                    <option value="{{ $i }}" {{ ($i == $currentYear) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="जनवरी - January" selected>जनवरी - January</option>
                                    <option value="फरवरी - February">फरवरी - February</option>
                                    <option value="मार्च - March">मार्च - March</option>
                                    <option value="अप्रैल - April">अप्रैल - April</option>
                                    <option value="मई - May">मई - May</option>
                                    <option value="जून - June">जून - June</option>
                                    <option value="जुलाई - July">जुलाई - July</option>
                                    <option value="अगस्त - August">अगस्त - August</option>
                                    <option value="सितम्बर - September">सितम्बर - September</option>
                                    <option value="अक्टूबर - October">अक्टूबर - October</option>
                                    <option value="नवम्बर - November">नवम्बर - November</option>
                                    <option value="दिसम्बर - December">दिसम्बर - December</option>
                                </select>
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