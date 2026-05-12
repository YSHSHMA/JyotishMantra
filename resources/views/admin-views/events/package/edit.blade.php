@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_package'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_package') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new Edit_package -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.event-managment.event_package.edit',[$getdata['id']]) }}" method="post" enctype="multipart/form-data">
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
                      
                        <!-- Input fields for event package name -->
                        @foreach($language as $lang)
                        <?php
                                            if (count($getdata['translations'])) {
                                                $translate = [];
                                                foreach ($getdata['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == 'package_name') {
                                                        $translate[$lang]['package_name'] = $translations->value;
                                                    }
                                                    if ($translations->locale == $lang && $translations->key == 'description') {
                                                        $translate[$lang]['description'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('package_name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="package_name[]" class="form-control" id="name" value="{{ $lang == $defaultLanguage ? $getdata['package_name'] : $translate[$lang]['package_name'] ?? '' }}" placeholder="{{ translate('enter_package_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">

                           <label class="title-color" for="name">{{ translate('description') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <textarea name="description[]" class="form-control ckeditor" id="name" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}>{{ $lang == $defaultLanguage ? $getdata['description'] : $translate[$lang]['description'] ?? '' }}</textarea>
                           

                        </div>
                        
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ getValidImage(path: 'storage/app/public/event/package/'.$getdata['image'], type: 'backend-product')  }}"
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
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     @if (Helpers::modules_permission_check('Event', 'Package', 'edit'))
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
<!-- Hidden HTML element for delete route -->
<span id="route-admin-event_package-delete" data-url="{{ route('admin.event-managment.event_package.delete') }}"></span>
<!-- Toast message for event package deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('event_package_deleted') }}
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
   "use strict";
   // Retrieve localized texts
   let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
   let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
   
   // Handle delete button click
   $('.event_package-delete-button').on('click', function () {
      let packageId = $(this).attr("id");
      Swal.fire({
         title: messageAreYouSureDeleteThis,
         text: messageYouWillNotAbleRevertThis,
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: getYesWord,
         cancelButtonText: getCancelWord,
         icon: 'warning',
         reverseButtons: true
      }).then((result) => {
         if (result.value) {
            // Send AJAX request to delete event caregory
            $.ajax({
               url: $('#route-admin-event_package-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: packageId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('event package deleted successfully', '', { positionClass: 'toast-bottom-left' });
                  // Reload the page
                  location.reload();
               },
               error: function (xhr, status, error) {
                  // Show error message
                  toastr.error(xhr.responseJSON.message);
               }
            });
         }
      });
   });
</script>
@endpush
