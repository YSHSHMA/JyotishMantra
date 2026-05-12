@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('event_organizer'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('event_organizer') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12 mb-2">
         <div class='row'>
            <div class="col-sm-6 col-lg-3 col-md-3">
               <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.organizers.list')}}">
                  <div class="order-stats__content">
                     <i class="tio-all_done">all_done</i>
                     <h6 class="order-stats__subtitle">{{ translate('All_organizer')}}</h6>
                  </div>
                  <span class="order-stats__title">
                     @php
                     echo \App\Models\EventOrganizer::count();
                     @endphp
                  </span>
               </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3">
               <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.organizers.list',['is_approve'=>1])}}">
                  <div class="order-stats__content">
                     <i class="tio-checkmark_circle_outlined text-success">checkmark_circle_outlined</i>
                     <h6 class="order-stats__subtitle">{{ translate('Approve')}}</h6>
                  </div>
                  <span class="order-stats__title">
                     @php
                     echo \App\Models\EventOrganizer::where('is_approve',1)->count();
                     @endphp
                  </span>
               </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3">
               <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.organizers.list',['is_approve'=>2])}}">
                  <div class="order-stats__content">
                     <i class="tio-clear_circle text-danger">clear_circle</i>
                     <h6 class="order-stats__subtitle">{{ translate('Reject')}}</h6>
                  </div>
                  <span class="order-stats__title">
                     @php
                     echo \App\Models\EventOrganizer::where('is_approve',2)->count();
                     @endphp</span>
               </a>
            </div>
            <div class="col-sm-6 col-lg-3 col-md-3">
               <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.organizers.list',['is_approve'=>0])}}">
                  <div class="order-stats__content">
                     <i class="tio-watch_later text-warning">watch_later</i>
                     <h6 class="order-stats__subtitle">{{ translate('Pending')}}</h6>
                  </div>
                  <span class="order-stats__title">
                     @php
                     echo \App\Models\EventOrganizer::where('is_approve',0)->count();
                     @endphp</span>
               </a>
            </div>
         </div>


      </div>


      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-8 col-md-6 col-lg-4 mb-2 mb-sm-0">
                     <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-custom input-group-merge">
                           <div class="input-group-prepend">
                              <div class="input-group-text">
                                 <i class="tio-search"></i>
                              </div>
                           </div>
                           <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                           <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                     </form>
                  </div>
                  <div class="col-sm-4 col-md-6 col-lg-8 text-right">
                     <a href="{{ route('admin.event-managment.organizers.add')}}" class="btn btn--primary">
                        <i class="tio-add"></i>
                        <span class="text">{{ translate('Add_organizer')}}</span>
                     </a>

                  </div>
               </div>
            </div>
            <!-- Table displaying event organizers -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Organizer_ID') }}</th>
                           <th>{{ translate('date_/_time_Registration') }}</th>
                           <th>{{ translate('Organizer_name') }}</th>
                           <th>{{ translate('person_name') }}</th>
                           <th>{{ translate('contact_info') }}</th>
                           <th>{{ translate('Approve_status') }}</th>
                           @if (Helpers::modules_permission_check('Event', 'Organizer', 'status'))
                           <th>{{ translate('status') }}</th>
                           @endif
                           <th>{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through items -->
                        @foreach($getData as $key => $items)
                        <tr>
                           <td>{{$getData->firstItem()+$key}}</td>
                           <td>
                              <a href="{{route('admin.event-managment.event.event-overview',['organizer'=>$items['id']])}}" class='font-weight-bold text-secondary'>
                                 {{ $items['unique_id'] }}
                                 <?php  
                                                $gettransactionRequest = \App\Models\EventApproTransaction::where(['types'=>'withdrawal','status'=>0,'organizer_id'=>$items['id']])->first();
                                            ?>
                                            @if(!empty($gettransactionRequest))
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                        <i class='tio-notifications_on_outlined'>notifications_on_outlined</i>
                                            </span>
                                            @endif
                              </a>
                           </td>
                           <td>{{ date('d M,Y h:i A',strtotime($items['created_at'])) }}</td>
                           <td>{{ $items['organizer_name'] }}</td>
                           <td> {{ $items['full_name'] }}</td>
                           <td>
                              {{ $items['contact_number'] }} <br>
                              {{ $items['email_address'] }}
                           </td>
                           <td> {{ (($items['is_approve'] == 1)?"Approve":(($items['is_approve'] == 2)?"Denite":"Pending" )) }}</td>
                           @if (Helpers::modules_permission_check('Event', 'Organizer', 'status'))
                           <td>
                              <form action="{{route('admin.event-managment.organizers.status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$items['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="items-status{{ $items['id'] }}" value="1" {{ $items['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="items-status{{ $items['id'] }}" data-on-image="items-status-on.png" data-off-image="items-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' Organizer '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' Organizer '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_event_organizer_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_event_organizer_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif
                           <!-- Actions for editing and deleting event organizers -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 @if (Helpers::modules_permission_check('Event', 'Organizer', 'detail'))
                                 <a class="btn btn-sm btn-outline-success" href="{{route('admin.event-managment.organizers.information',[$items['id']])}}"><i class="tio-invisible"></i></a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Event', 'Organizer', 'edit'))
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.event-managment.organizers.update',[$items['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 @endif
                                 @if (Helpers::modules_permission_check('Event', 'Organizer', 'delete'))
                                 <a class="event_organizer-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
                                    <i class="tio-delete"></i>
                                 </a>
                                 @endif
                              </div>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for event organizers list -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $getData->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($getData) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-event_organizer-delete" data-url="{{ route('admin.event-managment.organizers.delete') }}"></span>
<!-- Toast message for event organizers deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body">
         {{ translate('event_organizers_deleted') }}
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
   $('.event_organizer-delete-button').on('click', function() {
      let organizerId = $(this).attr("id");
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
               url: $('#route-admin-event_organizer-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: organizerId
               },
               success: function(response) {
                  // Show success message
                  toastr.success('event organizers deleted successfully', '', {
                     positionClass: 'toast-bottom-left'
                  });
                  // Reload the page
                  location.reload();
               },
               error: function(xhr, status, error) {
                  // Show error message
                  toastr.error(xhr.responseJSON.message);
               }
            });
         }
      });
   });
</script>
@endpush