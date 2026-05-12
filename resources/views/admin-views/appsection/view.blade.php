@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('app section'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('app_section_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new app section -->
      @if (Helpers::modules_permission_check('App Section', 'App Section', 'add'))
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.appsection.store') }}" method="post" enctype="multipart/form-data">
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
                        <!-- Input fields for app section name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                              placeholder="{{ translate('enter_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                        </div>
                        @endforeach
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

      <!-- Section for displaying app section list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('app_section_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $appsections->total() }}</span>
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
            <!-- Table displaying app section -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Name') }} </th>
                           @if (Helpers::modules_permission_check('App Section', 'App Section', 'status'))
                           <th class="text-center">{{ translate('status') }}</th>
                           @endif

                           @if (Helpers::modules_permission_check('App Section', 'App Section', 'edit') || Helpers::modules_permission_check('App Section', 'App Section', 'delete'))
                           <th class="text-center">{{ translate('action') }}</th>
                           @endif

                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through app section -->
                        @foreach($appsections as $key => $appsection)
                        <tr>
                           <td>{{$appsections->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$appsection['defaultname']}}">
                                   {{ Str::limit($appsection['defaultname'],20) }}
                               </span>
                           </td>

                           @if (Helpers::modules_permission_check('App Section', 'App Section', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.appsection.status-update') }}" method="post"
                                 id="appsection-status{{$appsection['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$appsection['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="appsection-status{{ $appsection['id'] }}" value="true"
                                       {{ $appsection['status'] == 'true' ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="appsection-status{{ $appsection['id'] }}"
                                       data-on-image="appsection-status-on.png"
                                       data-off-image="appsection-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$appsection['defaultname'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$appsection['defaultname'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif

                           @if (Helpers::modules_permission_check('App Section', 'App Section', 'edit') || Helpers::modules_permission_check('App Section', 'App Section', 'status'))
                           <!-- Actions for editing and deleting app section -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('App Section', 'App Section', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.appsection.update',[$appsection['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 @endif

                                 @if (Helpers::modules_permission_check('App Section', 'App Section', 'delete'))
                                 <a class="appsection-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $appsection['id'] }}">
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
            <!-- Pagination for app section list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $appsections->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($appsections) == 0)
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
<span id="route-admin-appsection-delete"
   data-url="{{ route('admin.appsection.delete') }}"></span>
<!-- Toast message for app section deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="app-section-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Section deleted') }}
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
   $('.appsection-delete-button').on('click', function () {
      let appsectionId = $(this).attr("id");
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
            // Send AJAX request to delete app section
            $.ajax({
               url: $('#route-admin-appsection-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: appsectionId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('appsection deleted successfully', '', { positionClass: 'toast-bottom-left' });
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
