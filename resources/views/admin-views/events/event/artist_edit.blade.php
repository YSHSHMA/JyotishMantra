@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_Artist'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_Artist') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new artist -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.event-managment.event.artist_edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <!-- Language tabs -->
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($language as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                     <div class="col-md-8">
                      
                        <!-- Input fields for artist name -->
                        @foreach($language as $lang)
                        <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'name') {
                                            $translate[$lang]['name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'profession') {
                                            $translate[$lang]['profession'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                    }
                                }
                                ?>

                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('artist_name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"  value="{{ $lang == $defaultLanguage ? $getData['name'] : $translate[$lang]['name'] ?? '' }}" placeholder="{{ translate('artist_name_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">

                           <label class="title-color" for="name">{{ translate('profession') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                           <textarea name="profession[]" class="form-control ckeditor" placeholder="{{ translate('Enter_profession') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ $lang == $defaultLanguage ? $getData['profession'] : $translate[$lang]['profession'] ?? '' }}</textarea>
                           <label class="title-color" for="description">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                           <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('Enter_description') }}"> {{ $lang == $defaultLanguage ? $getData['description'] : $translate[$lang]['description'] ?? '' }}</textarea>
                        </div>
                        
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer" src="{{ getValidImage(path: 'storage/app/public/event/events/'.$getData['image'], type: 'backend-product')  }}" alt="">
                        </div>
                        <div class="form-group">
                           <label for="detail_image" class="title-color">
                              {{ translate('artist_image') }}<span class="text-danger">*</span>
                           </label>
                           <span class="ml-1 text-info">
                              {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                           </span>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="image"
                                 class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     @if (Helpers::modules_permission_check('Event', 'Artist', 'edit'))
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                     @endif
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Section for displaying event categiry list -->
      
   </div>
</div>

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script>
 
</script>
@endpush
