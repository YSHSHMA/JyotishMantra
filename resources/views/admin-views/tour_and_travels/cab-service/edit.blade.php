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
               <form action="{{ route('admin.tour_cab_service.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
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
                        if (count($getData['translations'])) {
                           foreach ($getData['translations'] as $translations) {
                              if ($translations->locale == $lang && $translations->key == 'name') {
                                 $translate[$lang]['name'] = $translations->value;
                              }
                              if ($translations->locale == $lang && $translations->key == 'description') {
                                 $translate[$lang]['description'] = $translations->value;
                              }
                           }
                        }
                        ?>
                        <div class="form-group {{$lang != 'en' ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="col-md-6">
                                 <label class="title-color" for="name">{{ translate('select_type') }}<span class="text-danger">*</label>
                                 <select class="type_id form-control" onchange="getCategories();$('.type_id').val(this.value)" required>
                                    <option value="" selected disabled>{{ translate('select_type') }}</option>
                                    @if($vehicleList)
                                    @foreach($vehicleList as $k=>$val)
                                    <option value="{{ $val['id'] }}" {{ ((($vehicleCategorys[0]['type']??0) == $val['type'] )?'selected':'') }}>{{ $val['type'] }}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="name">{{ translate('select_vehicle_category') }}<span class="text-danger">*</label>
                                 <select name="vehicle_category" class="type_vehicle_cateogry_id form-control" onchange="$('.type_vehicle_cateogry_id').val(this.value)" required>
                                    <option value="" selected disabled>{{ translate('select_vehicle_category') }}</option>
                                    @if($vehicleCategorys)
                                    @foreach($vehicleCategorys as $values)
                                    <option value="{{ $values['id'] }}" {{ (($getData['vehicle_category'] == $values['id'] )?'selected':'') }}>{{ $values['brand_name'] }}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="name">{{ translate('cab_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <input type="text" name="name[]" class="form-control" id="name" value="{{ $translate[$lang]['name'] ?? $getData['name'] }}" placeholder="{{ translate('enter_cab_name') }}" {{$lang == 'en'? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="seat">{{ translate('cab_seats') }}</label>
                                 <input type="number" name="seats" value="{{old('seats',$getData['seats'])}}" onkeyup="$('.seats_set').val(this.value)" class="form-control seats_set" placeholder="{{ translate('enter_cab_seat') }}" {{$lang == 'en'? 'required':''}}>
                              </div>
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <textarea name="description[]" class="form-control ckeditor" id="name" placeholder="{{ translate('description') }}" {{$lang == 'en'? 'required':''}}>{{ $translate[$lang]['description'] ?? $getData['description'] }}</textarea>
                              </div>
                           </div>



                        </div>

                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/cab/'.$getData['image'], type: 'backend-product')  }}"
                              alt="">
                        </div>
                        <div class="form-group">
                           <label for="detail_image" class="title-color">
                              {{ translate('thumbnail') }}<span class="text-danger">*</span>
                           </label>
                           <span class="ml-1 text-info">
                              {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                           </span>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="image"
                                 class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer"
                                 accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <input type="hidden" name="id" value="{{ $getData['id']}}">
                     @if (Helpers::modules_permission_check('Tour', 'Tour Vehicle Setting', 'edit'))
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
    function getCategories() {
      $.ajax({
         url: "{{ route('admin.tour_cab_service.vehicle_category') }}",
         method: 'POST',
         data: {
            _token: '{{ csrf_token() }}',
            id: $('.type_id').val()
         },
         success: function(response) {
            if (response.success == 1) {
               let html = `<option value="" selected disabled>{{ translate('select_vehicle_category') }}</option>`;
               response.data.forEach(function(item) {
                  html += `<option value="${item.id}">${item.brand_name}</option>`;
               });
               $('.type_vehicle_cateogry_id').html(html);
            } else {
               toastr.error(response.message, '', {
                  positionClass: 'toast-bottom-left'
               });
            }
         }
      })
   }
</script>
@endpush