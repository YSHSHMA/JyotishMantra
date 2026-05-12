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
                                    <option value="cities_tour" {{ (( old('type',$getData['type']) == 'cities_tour' )?'selected':'' )}}> cities tour</option>
                                    <option value="special_tour" {{ (( old('type',$getData['type']) == 'special_tour' )?'selected':'' )}}>special tour</option>
                                 </select>
                              </div>
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Enter_Refund_percentage') }}</label>
                                 <input type="text" name="percentage" class="form-control percentages" onkeyup="$('.percentages').val(this.value)" value="{{ old('percentage',$getData['percentage']) }}" placeholder="{{ translate('Enter_Refund_percentage') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="col-md-4">
                              <label class="title-color" for="name">{{ translate('Enter_Refund_day') }}</label>
                              <input type="text" name="day" class="form-control days_refund" onkeyup="$('.days_refund').val(this.value)" value="{{ old('day',$getData['day'])}}" placeholder="{{ translate('Enter_Refund_day') }}" {{$lang == $defaultLanguage? 'required':''}}>
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
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endpush
