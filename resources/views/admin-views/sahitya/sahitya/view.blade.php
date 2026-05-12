@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('sahitya'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('sahitya_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new sahitya -->
      @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'add'))
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.sahitya.store') }}" method="post" enctype="multipart/form-data">
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
                        <!-- Input fields for sahitya name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <div class="form-group">
                           <label class="title-color" for="name">{{ translate('Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                              placeholder="{{ translate('enter_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           </div>

                       <input type="hidden" name="lang[]" value="{{ $lang }}">
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
      @endif

      <!-- Section for displaying sahityalist -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('sahitya_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $sahityas->total() }}</span>
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
            <!-- Table displaying sahitya -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Name') }} </th>
                           <th>{{ translate('Image') }} </th>
                           @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'status'))
                           <th class="text-center">{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'edit') || Helpers::modules_permission_check('Sahitya', 'Sahitya', 'delete'))
                           <th class="text-center">{{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through sahitya -->
                        @foreach($sahityas as $key => $sahitya)
                        <tr>
                           <td>{{$sahityas->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$sahitya['defaultname']}}">
                                   {{ Str::limit($sahitya['defaultname'],20) }}
                               </span>
                           </td>
                            <td>
                                <div class="avatar-60 d-flex align-items-center rounded">
                                    <img class="img-fluid" alt=""
                                         src="{{ getValidImage(path: 'storage/app/public/sahitya/'.$sahitya['image'], type: 'backend-sahitya') }}">
                                </div>
                            </td>
                            @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.sahitya.status-update') }}" method="post"
                                 id="sahitya-status{{$sahitya['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$sahitya['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="sahitya-status{{ $sahitya['id'] }}" value="1" {{ $sahitya['status'] == 1 ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="sahitya-status{{ $sahitya['id'] }}"
                                       data-on-image="sahitya-status-on.png"
                                       data-off-image="sahitya-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$sahitya['defaultname'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$sahitya['defaultname'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif

                           @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'edit') || Helpers::modules_permission_check('Sahitya', 'Sahitya', 'delete'))
                           <!-- Actions for editing and deleting sahitya -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.sahitya.update',[$sahitya['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'delete'))
                                 <a class="sahitya-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $sahitya['id'] }}">
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
            <!-- Pagination for sahityalist -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $sahityas->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($sahityas) == 0)
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
<span id="route-admin-sahitya-delete"
   data-url="{{ route('admin.sahitya.delete') }}"></span>
<!-- Toast message for sahityadeleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="sahitya-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Section deleted') }}
      </div>
   </div>
</div>
@foreach($sahityas as $sahitya)

<div class="modal fade" id="imageModal{{$sahitya['id']}}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel{{$sahitya['id']}}" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel{{$sahitya['id']}}">{{ translate('Image Preview') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body text-center">
           <div class="row">
                  @php
                      $images = json_decode($sahitya['image'], true);
                  @endphp
                  @if($images)
                     @foreach($images as $image)
                        <div class="col-md-4 mb-3">
                           <img class="img-fluid rounded" alt="Sahitya Image"
                              src="{{ getValidImage(path: 'storage/app/public/sahitya/' . $image, type: 'backend-sahitya') }}">
                        </div>
                     @endforeach
                  @else
                     <p>{{ translate('No Images Available') }}</p>
                  @endif
               </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
         </div>
      </div>
   </div>
</div>

@endforeach

@endsection


@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

 {{-- datepicker --}}
 <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
 <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
 <script type="text/javascript">
     $(document).ready(function() {
         $('.ckeditor').ckeditor();
     });
 </script>


<script>
   "use strict";
   // Retrieve localized texts
   let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
   let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
   
   // Handle delete button click
   $('.sahitya-delete-button').on('click', function () {
      let sahityaId = $(this).attr("id");
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
            // Send AJAX request to delete sahitya
            $.ajax({
               url: $('#route-admin-sahitya-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: sahityaId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('sahitya deleted successfully', '', { positionClass: 'toast-bottom-left' });
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