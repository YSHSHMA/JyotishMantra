@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_cab_setting'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_cab_setting') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new Edit_cab_setting -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.tour_type.edit',[$getDatalist['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($languages as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == 'en'? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                     <div class="col-md-8">

                        @foreach($languages as $lang)
                        <?php
                        $translate = [];
                        if (count($getDatalist['translations'])) {
                           foreach ($getDatalist['translations'] as $translations) {
                              if ($translations->locale == $lang && $translations->key == 'name') {
                                 $translate[$lang]['name'] = $translations->value;
                              }
                           }
                        }
                        ?>
                        <div class="form-group {{$lang != 'en' ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('Tour_type_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <input type="text" name="name[]" class="form-control" value="{{ $translate[$lang]['name'] ?? $getDatalist['name'] }}" placeholder="{{ translate('enter_tour_Type_name') }}" {{$lang == 'en'? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" >
                              </div>
                              
                           </div>



                        </div>

                        @endforeach
                     </div>
                     
                  </div>
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <input type="hidden" name="id" value="{{ $getDatalist['id']}}">
                     @if (Helpers::modules_permission_check('Tour', 'Tour Category', 'edit'))
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
@endpush