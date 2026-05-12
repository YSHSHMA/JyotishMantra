@extends('layouts.back-end.app')

@section('title', translate('video subcategory'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/logo.png') }}" alt="">
         {{ translate('video_Subcategory_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new video subcategory -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.videosubcategory.store') }}" method="post" enctype="multipart/form-data">
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
                        <div class="form-group">
                           <label for="category_id" class="title-color">
                              {{ translate('Video Category') }}
                              <span class="text-danger">*</span>
                           </label>
                           <select name="category_id" class="form-control" id="category_id" required>
                              <option value="">{{ translate('Select Category') }}</option>
                              @foreach($videoCategories as $category)
                              <option value="{{ $category->id }}">{{ $category->name }}</option>
                              @endforeach
                           </select>
                        </div>
                        <!-- Input fields for video subcategory name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('subcategory_Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                              placeholder="{{ translate('subcategory_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                        </div>
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
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
                                 required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
                           </div>
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

      <!-- Section for displaying video subcategory list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('subcategory_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $videosubcategorys->total() }}</span>
                     </h5>
                  </div>
                  <div class="col-sm-8 col-md-6 col-lg-4">
                     <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-custom input-group-merge">
                           <div class="input-group-prepend">
                              <div class="input-group-text">
                                 <i class="tio-search"></i>
                              </div>
                           </div>
                           <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                             placeholder="{{ translate('search_by_name') }}"
                              aria-label="{{ translate('search_by_name') }}" required>
                           <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <!-- Table displaying video subcategories -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('category_Name') }} </th>
                           <th>{{ translate('subcategory_Name') }} </th>
                           <th>{{ translate('image') }}</th>
                           <th class="text-center">{{ translate('status') }}</th>
                           <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through video subcategories -->
                        @foreach($videosubcategorys as $key => $videosubcategory)
                        <tr>
                           <td>{{$videosubcategorys->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                            @php
                             $hindiName = $videosubcategory->category->translations->isNotEmpty()
                             ? $videosubcategory->category->translations->first()->value
                             : $videosubcategory->category->name;
                              @endphp
                        <span data-toggle="tooltip" data-placement="right" title="{{ $hindiName }}">
                            {{ Str::limit($hindiName, 20) }}
                        </span>
                          </td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$videosubcategory['defaultname']}}">
                                   {{ Str::limit($videosubcategory['defaultname'],20) }}
                               </span>
                           </td>
                           <td>
                              <div class="avatar-60 d-flex align-items-center rounded">
                                 <img class="img-fluid" alt=""
                                    src="{{ getValidImage(path: 'storage/app/public/video-subcategory-img/' . $videosubcategory['image'], type: 'backend-video') }}">
                              </div>
                           </td>
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.videosubcategory.status-update') }}" method="post"
                                 id="videosubcategory-status{{$videosubcategory['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$videosubcategory['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="videosubcategory-status{{ $videosubcategory['id'] }}" value="1"
                                       {{ $videosubcategory['status'] == 1 ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="videosubcategory-status{{ $videosubcategory['id'] }}"
                                       data-on-image="videosubcategory-status-on.png"
                                       data-off-image="videosubcategory-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$videosubcategory['defaultname'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$videosubcategory['defaultname'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_video_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_video_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           <!-- Actions for editing and deleting video subcategories -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.videosubcategory.update',[$videosubcategory['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 <a class="videosubcategory-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $videosubcategory['id'] }}">
                                    <i class="tio-delete"></i>
                                 </a>
                              </div>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for video subcategory list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $videosubcategorys->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($videosubcategorys) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160"
                  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-videosubcategory-delete"
   data-url="{{ route('admin.videosubcategory.delete') }}"></span>
<!-- Toast message for video deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="video-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Video deleted') }}
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
   $('.videosubcategory-delete-button').on('click', function () {
      let videosubcategoryId = $(this).attr("id");
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
            // Send AJAX request to delete video subcategory
            $.ajax({
               url: $('#route-admin-videosubcategory-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: videosubcategoryId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('videosubcategory deleted successfully', '', { positionClass: 'toast-bottom-left' });
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
   $(document).ready(function() {
       // When the subcategory name input changes
       $('input[name="name[]"]').on('change', function() {
           var categoryId = $('#category_id').val();
           var subcategoryName = $(this).val();

           // Perform AJAX request to check if the subcategory name already exists
           $.ajax({
               url: '{{ route("admin.videosubcategory.store") }}', // Use the same route as your form submission route
               type: 'POST',
               data: {
                   _token: '{{ csrf_token() }}',
                   category_id: categoryId,
                   name: subcategoryName,
               },
               success: function(response) {
                   // Handle response here
                   if (response.exists) {
                       toastr.error('This subcategory name is already associated with the selected category.');
                       // Optionally disable form submission or display an error next to the input
                   }
               },
               error: function(xhr, status, error) {
                   toastr.error('This subcategory name is already associated with the selected category.');
               }
           });
       });
   });
</script>
@endpush
