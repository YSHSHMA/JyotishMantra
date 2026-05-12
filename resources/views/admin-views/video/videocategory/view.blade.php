@extends('layouts.back-end.app')
@section('title', translate('video category'))
@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/logo.png') }}" alt="">
         {{ translate('video_Category_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.videocategory.store') }}" method="post" class="text-start">
                  @csrf
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
                  @foreach($language as $lang)
                  <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                     id="{{$lang}}-form">
                     <input type="hidden" id="id">
                     <label class="title-color" for="name">{{ translate('Category_Name') }}<span
                        class="text-danger">*</span>
                     ({{ strtoupper($lang) }})</label>
                     <input type="text" name="name[]" class="form-control" id="name"
                     placeholder="{{ translate('category_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                  </div>
                  <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                  @endforeach
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <div id="message-are-you-sure-delete-this" data-text="{{ translate('Are you sure you want to delete this video category?') }}" style="display: none;"></div>
      <div id="message-you-will-not-be-able-to-revert-this" data-text="{{ translate('You will not be able to revert this!') }}" style="display: none;"></div>
      <div id="message-yes-word" data-text="{{ translate('Yes') }}" style="display: none;"></div>
      <div id="message-cancel-word" data-text="{{ translate('Cancel') }}" style="display: none;"></div>
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('category_list') }}
                        <span
                           class="badge badge-soft-dark radius-50 fz-12">{{ $videocategorys->total() }}</span>
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
                           <input id="datatableSearch_" type="search" name="searchValue"
                              class="form-control"
                              placeholder="{{ translate('search_by_name') }}"
                              aria-label="{{ translate('search_by_name') }}"" required>
                           <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('category_Name') }} </th>
                           <th class="text-center">{{ translate('status') }}</th>
                           <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($videocategorys as $key => $videocategory)
                        <tr>
                           <td>{{$videocategorys->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$videocategory['defaultname']}}">
                                   {{ Str::limit($videocategory['defaultname'],20) }}
                               </span>
                           </td>
                           <td>
                              <form action="{{route('admin.videocategory.status-update') }}" method="post" id="videocategory-status{{$videocategory['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$videocategory['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                    id="videocategory-status{{ $videocategory['id'] }}" value="1" {{ $videocategory['status'] == 1 ? 'checked' : '' }}
                                    data-modal-id = "toggle-status-modal"
                                    data-toggle-id = "videocategory-status{{ $videocategory['id'] }}"
                                    data-on-image = "videocategory-status-on.png"
                                    data-off-image = "videocategory-status-off.png"
                                    data-on-title = "{{ translate('Want_to_Turn_ON').' '.$videocategory['defaultname'].' '. translate('status') }}"
                                    data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$videocategory['defaultname'].' '.translate('status') }}"
                                    data-on-message = "
                                    <p>{{ translate('if_enabled_this_video_will_be_available_on_the_website_and_customer_app') }}</p>
                                    "
                                    data-off-message = "
                                    <p>{{ translate('if_disabled_this_video_will_be_hidden_from_the_website_and_customer_app') }}</p>
                                    ">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.videocategory.update',[$videocategory['id']])}}">
                                 <i class="tio-edit"></i>
                                 </a>
                                 <a class="videocategory-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $videocategory['id'] }}">
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
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $videocategorys->links() !!}
               </div>
            </div>
            @if(count($videocategorys) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
<!-- HTML element for route -->
<span id="route-admin-videocategory-delete" data-url="{{ route('admin.videocategory.delete') }}"></span>
<!-- Message for video deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="video-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body">
         {{ translate('Video deleted') }}
      </div>
   </div>
</div>
</div>
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
   "use strict";
      let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
   let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
   
          $('.videocategory-delete-button').on('click', function () {
              let videocategoryId = $(this).attr("id");
              Swal.fire({
                  title: messageAreYouSureDeleteThis,
          text: messageYouWillNotAbleRevertThis,
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: getYesWord,
          cancelButtonText: getCancelWord,
          type: 'warning',
          reverseButtons: true
              }).then((result) => {
                  if (result.value) {
                      // Send AJAX request to delete video category
                      $.ajax({
                          url: $('#route-admin-videocategory-delete').data('url'),
                          method: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}', // Include CSRF token
                              id: videocategoryId
                          },
                          success: function (response) {
                              // Show success message
                                toastr.success('Videocategory deleted successfully', '', { positionClass: 'toast-bottom-left' });
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