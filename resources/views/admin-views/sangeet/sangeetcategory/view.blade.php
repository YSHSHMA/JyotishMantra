@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('sangeet category'))
@section('content')
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/logo.png') }}" alt="">
         {{ translate('sangeet_Category_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new sangeetcategory -->
      @if (Helpers::modules_permission_check('Sangeet', 'Category', 'add'))
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.sangeetcategory.store') }}" method="post" enctype="multipart/form-data">
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
                      <div class="col-md-6">
                       @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('sangeetcategory_Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                           placeholder="{{ translate('category_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                        </div>
                        @endforeach
                           <div class="form-group">
                           <label for="image" class="title-color">
                           {{ translate('thumbnail') }}<span class="text-danger">*</span>
                           </label>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="image"
                                 class="custom-file-input image-preview-before-upload" data-preview="#image-viewer"
                                 required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="image">
                              {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                         <div class="form-group">
                           <label for="banner" class="title-color">
                           {{ translate('banner') }}<span class="text-danger">*</span>
                           </label>
                           <div class="custom-file text-left">
                              <input type="file" name="banner" id="banner"
                                 class="custom-file-input banner-preview-before-upload" data-preview="#banner-viewer"
                                 required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="banner">
                              {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 mb-4">
                        <div class="text-center mb-4">
                           <img class="upload-img-view" id="image-viewer"
                              src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                              alt="">
                        </div>
                         <div class="text-center">
                           <img class="upload-img-view" id="banner-viewer"
                              src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                              alt="">
                        </div>
                     </div>
                  </div>
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      @endif
      <!-- Section for displaying sangeetcategory list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('sangeet_category_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $sangeetcategorys->total() }}</span>
                     </h5>
                  </div>

               </div>
            </div>
            <!-- Table displaying sangeetcategory -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="example"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('name') }}</th>
                           <th>{{ translate('image') }}</th>
                           <th>{{ translate('banner') }}</th>
                           @if (Helpers::modules_permission_check('Sangeet', 'Category', 'status'))
                           <th class="text-center">{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Sangeet', 'Category', 'edit') || Helpers::modules_permission_check('Sangeet', 'Category', 'delete'))
                           <th class="text-center">{{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through sangeetcategory -->
                        @foreach($sangeetcategorys as $key => $sangeetcategory)
                        <tr>
                           <td>{{$sangeetcategorys->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                              <span data-toggle="tooltip" data-placement="right" title="{{$sangeetcategory['defaultname']}}">
                              {{ Str::limit($sangeetcategory['defaultname'],20) }}
                              </span>
                           </td>
                           <td>
                              <div class="avatar-60 d-flex align-items-center rounded">
                                 <img class="img-fluid" alt=""
                                    src="{{ getValidImage(path: 'storage/app/public/sangeet-category-img/' . $sangeetcategory['image'], type: 'backend-sangeetcategory') }}" style="width: 80px; height: 60px;">
                              </div>
                           </td>
                           <td>
                              <div class="avatar-60 d-flex align-items-center rounded">
                                 <img class="img-fluid" alt=""
                                    src="{{ getValidImage(path: 'storage/app/public/sangeet-category-banner/' . $sangeetcategory['banner'], type: 'backend-sangeetcategory') }}" style="width: 110px; height: 60px;">
                              </div>
                           </td>
                           @if (Helpers::modules_permission_check('Sangeet', 'Category', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.sangeetcategory.status-update') }}" method="post"
                                 id="sangeetcategory-status{{$sangeetcategory['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$sangeetcategory['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                    id="sangeetcategory-status{{ $sangeetcategory['id'] }}" value="1"
                                    {{ $sangeetcategory['status'] == 1 ? 'checked' : '' }}
                                    data-modal-id="toggle-status-modal"
                                    data-toggle-id="sangeetcategory-status{{ $sangeetcategory['id'] }}"
                                    data-on-image="sangeetcategory-status-on.png"
                                    data-off-image="sangeetcategory-status-off.png"
                                    data-on-title="{{ translate('Want_to_Turn_ON').' '.$sangeetcategory['defaultname'].' '. translate('status') }}"
                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.$sangeetcategory['defaultname'].' '.translate('status') }}"
                                    data-on-message="
                                    <p>{{ translate('if_enabled_this_sangeetcategory_will_be_available_on_the_website_and_customer_app') }}</p>
                                    "
                                    data-off-message="
                                    <p>{{ translate('if_disabled_this_sangeetcategory_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                    ">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif

                           @if (Helpers::modules_permission_check('Sangeet', 'Category', 'edit') || Helpers::modules_permission_check('Sangeet', 'Category', 'delete'))
                           <!-- Actions for editing and deleting sangeetcategory -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('Sangeet', 'Category', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.sangeetcategory.update',[$sangeetcategory['id']])}}">
                                 <i class="tio-edit"></i>
                                 </a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Sangeet', 'Category', 'delete'))
                                 <a class="sangeetcategory-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $sangeetcategory['id'] }}">
                                 <i class="tio-delete"></i>
                                 </a>
                                 @endif
                              </div>
                           </td>
                           @endif
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for sangeetcategory list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $sangeetcategorys->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
<!--             @if(count($sangeetcategorys) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160"
                  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif -->
         </div>
      </div>
   </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-sangeetcategory-delete"
   data-url="{{ route('admin.sangeetcategory.delete') }}"></span>
<!-- Toast message for sangeetcategory deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="sangeetcategory-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('sangeetcategory deleted') }}
      </div>
   </div>
</div>
@endsection
@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
   "use strict";
   // Retrieve localized texts
   let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
   let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
   
   // Handle delete button click
   $('.sangeetcategory-delete-button').on('click', function () {
      let sangeetcategoryId = $(this).attr("id");
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
            // Send AJAX request to delete sangeetcategory
            $.ajax({
               url: $('#route-admin-sangeetcategory-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: sangeetcategoryId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('sangeetcategory deleted successfully', '', { positionClass: 'toast-bottom-left' });
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


<script>
   $(document).ready(function () {
       // Language tab toggle
       $(".form-system-language-tab").on("click", function () {
           $(".form-system-language-tab").removeClass("active");
           $(".form-system-language-form").addClass("d-none");
           $("#" + $(this).attr("id").replace("-link", "") + "-form").removeClass("d-none");
           $(this).addClass("active");
       });

       // Image preview functionality
       function previewImage(input, previewElement) {
           if (input.files && input.files[0]) {
               var reader = new FileReader();
               reader.onload = function (e) {
                   $(previewElement).attr('src', e.target.result);
               };
               reader.readAsDataURL(input.files[0]);
           }
       }

       // Event listeners for image and banner input changes
       $("#image").change(function () {
           previewImage(this, '#image-viewer');
       });
       $("#banner").change(function () {
           previewImage(this, '#banner-viewer');
       });
   });
</script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
 <script>
     $(document).ready(function() {
         $('#example').DataTable({
             searching: true,
             paging: false,
             ordering: true,
             info: true
         });
     });
 </script>
@endpush