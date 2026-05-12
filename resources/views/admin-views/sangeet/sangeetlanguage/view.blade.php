@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('sangeet Language'))

@section('content')
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/logo.png') }}" alt="">
         {{ translate('sangeet_Language_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new sangeet language -->
      @if (Helpers::modules_permission_check('Sangeet', 'Language', 'add'))
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.sangeetlanguage.store') }}" method="post" enctype="multipart/form-data">
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
                        <!-- Input fields for sangeet language name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <label class="title-color" for="name">{{ translate('Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                              placeholder="{{ translate('language_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
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

      <!-- Section for displaying sangeet  list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('language_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $sangeetlanguages->total() }}</span>
                     </h5>
                  </div>

               </div>
            </div>
            <!-- Table displaying sangeet subcategories -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="example"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('language_Name') }} </th>
                           @if (Helpers::modules_permission_check('Sangeet', 'Language', 'add'))
                           <th class="text-center">{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Sangeet', 'Language', 'edit') || Helpers::modules_permission_check('Sangeet', 'Language', 'delete'))
                           <th class="text-center">{{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through sangeet subcategories -->
                        @foreach($sangeetlanguages as $key => $sangeetlanguage)
                        <tr>
                           <td>{{$sangeetlanguages->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$sangeetlanguage['defaultname']}}">
                                   {{ Str::limit($sangeetlanguage['defaultname'],20) }}
                               </span>
                           </td>
                           @if (Helpers::modules_permission_check('Sangeet', 'Language', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.sangeetlanguage.status-update') }}" method="post"
                                 id="sangeetlanguage-status{{$sangeetlanguage['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$sangeetlanguage['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="sangeetlanguage-status{{ $sangeetlanguage['id'] }}" value="1"
                                       {{ $sangeetlanguage['status'] == 1 ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="sangeetlanguage-status{{ $sangeetlanguage['id'] }}"
                                       data-on-image="sangeetlanguage-status-on.png"
                                       data-off-image="sangeetlanguage-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$sangeetlanguage['defaultname'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$sangeetlanguage['defaultname'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_sangeet_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_sangeet_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif

                           @if (Helpers::modules_permission_check('Sangeet', 'Language', 'edit') || Helpers::modules_permission_check('Sangeet', 'Language', 'delete'))
                           <!-- Actions for editing and deleting sangeet subcategories -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('Sangeet', 'Language', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.sangeetlanguage.update',[$sangeetlanguage['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Sangeet', 'Language', 'delete'))
                                 <a class="sangeetlanguage-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $sangeetlanguage['id'] }}">
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
            <!-- Pagination for sangeet list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $sangeetlanguages->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
<!--             @if(count($sangeetlanguages) == 0)
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
<span id="route-admin-sangeetlanguage-delete"
   data-url="{{ route('admin.sangeetlanguage.delete') }}"></span>
<!-- Toast message for sangeet deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="sangeet-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('sangeet deleted') }}
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
   $('.sangeetlanguage-delete-button').on('click', function () {
      let sangeetlanguageId = $(this).attr("id");
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
            // Send AJAX request to delete sangeet language
            $.ajax({
               url: $('#route-admin-sangeetlanguage-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: sangeetlanguageId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('sangeetlanguage deleted successfully', '', { positionClass: 'toast-bottom-left' });
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
