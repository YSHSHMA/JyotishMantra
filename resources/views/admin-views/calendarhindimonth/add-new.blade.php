@extends('layouts.back-end.app')
@section('title', translate('calendar_hindi_month_Add'))
@section('content')
@push('css_or_js')
{{-- datepicker --}}
<link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pradoshkatha.png') }}" alt="">
         {{ translate('calendar_hindi_month_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.calendarhindimonth.add-new') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hindi Month Title</label>
                                <input type="text" name="hindimonth_title" class="form-control" placeholder="Enter Calendar Hindi Month Title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hindi Month Year</label>
                                <select name="date_month_year[]" id="date_month_year" class="form-control">
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
                                <label>Hindi Month</label>
                                <select name="date_month_year[]" id="date_month_year" required class="form-control">
                                        <option value="">Select Month</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                        $value = ($i < 10)?'0'.$i:$i;
                                        @endphp
                                            <option value="{{ $value }}">{{ date("F", mktime(0, 0, 0, $i, 1)) }}</option>
                                        @endfor
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