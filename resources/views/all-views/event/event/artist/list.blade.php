@extends('layouts.back-end.app-event')

@section('title', translate('Artist_list'))

@section('content')
@php 
use App\Utils\Helpers;
if (auth('event')->check()) {
$relationEmployees = auth('event')->user()->relation_id;
} elseif (auth('event_employee')->check()) {
$relationEmployees = auth('event_employee')->user()->relation_id;
}
@endphp
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Artist_list') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new artist -->
      <!-- Section for displaying event categiry list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('artist_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ ($getData->total()??'') }}</span>
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
            <!-- Table displaying event package -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('image') }}</th>
                           <th>{{ translate('name') }}</th>
                           <th>{{ translate('profession') }}</th>
                           <th>{{ translate('status') }}</th>
                           <th>{{ translate('created_by') }}</th>
                           <th>{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through items -->
                        @foreach($getData as $key => $items)
                        <tr>
                           <td>{{$getData->firstItem()+$key}}</td>
                           <td>
                              <img style="width: 66px; height: 65px;" alt="" src="{{ getValidImage(path: 'storage/app/public/event/events/' . $items['image'], type: 'backend-panchang') }}">
                           </td>
                           <td>
                                 {{ ucwords($items['name']) }}
                        </td>
                           <td>
                           {!! Str::limit(strip_tags($items['profession'] ?? ""), 20) !!}
                           </td>
                           <td>
                              <span class="badge badge-pill ml-1 badge-soft-{{ $items['status'] == 1 ? 'success' : 'danger' }}">{{ $items['status'] == 1 ? 'Active' : 'In-Active' }}</span>
                           </td>
                           <td>{{ $items['created_by'] == 0 ? 'Admin' : 'Vendor' }}</td>
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::Employee_modules_permission('Artist Management', 'Artist List', 'View'))
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('view') }}" href="{{route('event-vendor.artist.artist_update',[$items['id'],'type'=>'view'])}}">
                                    <i class="tio-visible"></i>
                                 </a>
                                 @endif
                                 @if($items['created_by'] == $relationEmployees)
                                 @if (Helpers::Employee_modules_permission('Artist Management', 'Artist List', 'Edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('event-vendor.artist.artist_update',[$items['id'],'type'=>'edit'])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 @endif
                                 @endif
                              </div>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for event package list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $getData->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($getData) == 0)
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
<span id="route-admin-event_artist-delete" data-url="{{ route('admin.event-managment.event.artist_delete') }}"></span>
<!-- Toast message for event artist deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('event_artist_deleted') }}
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
   $('.event_artist-delete-button').on('click', function () {
      let artistId = $(this).attr("id");
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
            $.ajax({
               url: $('#route-admin-event_artist-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: artistId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('event artist deleted successfully', '', { positionClass: 'toast-bottom-left' });
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
