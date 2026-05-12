@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_policy'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_policy') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new Edit_policy -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.tour-refund-policy.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($languages as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                     <div class="col-md-12">

                        <!-- Input fields for event package name -->
                        @foreach($languages as $lang)
                        <?php
                        if (count($getData['translations'])) {
                           $translate = [];
                           foreach ($getData['translations'] as $translations) {
                              if ($translations->locale == $lang && $translations->key == 'message') {
                                 $translate[$lang]['message'] = $translations->value;
                              }
                           }
                        }
                        ?>
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Select Type') }}</label>
                                 <select name="type" class="form-control tour_type" onchange="$('.tour_type').val(this.value)" {{$lang == $defaultLanguage? 'required':''}}>
                                    <option value="">Select Type</option>
                                    @if(!empty($gettypelist) && count($gettypelist))
                                    @foreach($gettypelist as $val)
                                    <option value="{{$val['slug']}}" {{ (( old('type',$getData['type']) == $val['slug'] )?'selected':'' )}}> {{ $val['name'] }}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Enter_Refund_percentage') }}</label>
                                 <input type="text" name="percentage" class="form-control percentages" onkeyup="$('.percentages').val(this.value)" value="{{ old('percentage',$getData['percentage']) }}" placeholder="{{ translate('Enter_Refund_percentage') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Enter_Refund_day') }}</label>
                                 <?php
                                 $totalHours = old('day', $getData['day'] ?? 0);
                                 $timeUnit = ($totalHours % 24 === 0) ? 'day' : 'hours';
                                 $timeValue = ($timeUnit === 'day') ? ($totalHours / 24) : $totalHours;
                                 ?>
                                 <select name="time_unit" class="form-control hourSelect house_options" onchange="$('.house_options').val(this.value);updateConvertedHours()">
                                    <option value="">-- Select --</option>
                                    <option value="hours" {{ old('time_unit',$timeUnit) == 'hours' ? 'selected' : '' }}>Hours</option>
                                    <option value="day" {{ old('time_unit',$timeUnit) == 'day' ? 'selected' : '' }}>Days</option>
                                 </select>
                                 <input type="number" class="form-control mt-2 user_enter_hours_days hours_days" name="time_value" onkeyup="$('.hours_days').val(this.value);updateConvertedHours()" value="{{ old('time_value',$timeValue) }}" placeholder="{{ translate('Enter_Refund_day_hours') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="day" class="form-control convert_hours" value="{{ old('day',$getData['day'])}}" placeholder="{{ translate('Enter_Refund_day') }}" {{$lang == $defaultLanguage? 'required':''}}>
                              </div>
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('message') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <textarea name="message[]" class="form-control ckeditor" placeholder="{{ translate('message') }}" {{$lang == $defaultLanguage? 'required':''}}>{{ old('message.'.$loop->index,($lang == $defaultLanguage ? $getData['message'] : $translate[$lang]['message'] ?? '')) }}</textarea>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </div>

                  </div>
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <input type="hidden" name="id" value="{{ $getData['id']}}">
                     @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'edit'))
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                     @endif
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
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
   function updateConvertedHours() {
      const type = document.querySelector('.hourSelect').value;
      const value = parseInt(document.querySelector('.user_enter_hours_days').value);
      let totalHours = 0;
      if (!isNaN(value)) {
         if (type === 'hours') {
            totalHours = value;
         } else if (type === 'day') {
            totalHours = value * 24;
         }
      }
      $(".convert_hours").val(totalHours);
   }
</script>
@endpush