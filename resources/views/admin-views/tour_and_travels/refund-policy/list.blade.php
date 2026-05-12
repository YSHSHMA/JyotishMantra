@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('tour_refund_policy'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('tour_refund_policy') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new tour_refund_policy -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.tour-refund-policy.store') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <!-- Language tabs -->
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($languages as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                     <div class="col-md-12">

                        <!-- Input fields for tour package name -->
                        @foreach($languages as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Select Type') }}</label>
                                 <select name="type" class="form-control tour_type" onchange="$('.tour_type').val(this.value)" {{$lang == $defaultLanguage? 'required':''}}>
                                    <option value="">Select Type</option>
                                    @if(!empty($gettypelist) && count($gettypelist))
                                    @foreach($gettypelist as $val)
                                    <option value="{{$val['slug']}}" {{ (( old('type') == $val['slug'] )?'selected':'' )}}> {{ $val['name'] }}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Enter_refund_percentage') }}</label>
                                 <input type="text" name="percentage" class="form-control percentages" onkeyup="$('.percentages').val(this.value)" value="{{ old('percentage')}}" placeholder="{{ translate('Enter_refund_percentage') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="col-md-4">
                                 <label class="title-color" for="name">{{ translate('Enter_Refund_day/hours') }}</label>
                                 <select name="time_unit" class="form-control hourSelect house_options" onchange="$('.house_options').val(this.value);updateConvertedHours()">
                                    <option value="">-- Select --</option>
                                    <option value="hours" {{ old('time_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
                                    <option value="day" {{ old('time_unit') == 'day' ? 'selected' : '' }}>Days</option>
                                 </select>
                                 <input type="number" class="form-control mt-2 user_enter_hours_days hours_days" name="time_value" onkeyup="$('.hours_days').val(this.value);updateConvertedHours()" value="{{ old('time_value') }}" placeholder="{{ translate('Enter_Refund_day_hours') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="day" class="form-control convert_hours" value="{{ old('day')}}">
                              </div>
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('message') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <textarea name="message[]" class="form-control ckeditor" placeholder="{{ translate('message') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('message.'.$loop->index)}} </textarea>
                              </div>
                           </div>

                        </div>

                        @endforeach
                     </div>

                  </div>
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'add'))
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                     @endif
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Section for displaying tour categiry list -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('Package_list') }}
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
            <!-- Table displaying tour package -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('type') }}</th>
                           <th>{{ translate('day') }}</th>
                           <th>{{ translate('percentage') }}</th>
                           <th>{{ translate('message') }}</th>
                           @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'status'))
                           <th>{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'edit') || Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'delete'))
                           <th>{{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through items -->
                        @foreach($getData as $key => $items)
                        <tr>
                           <td>{{$getData->firstItem()+$key}}</td>
                           <td>{{ translate($items['type']) }}</td>
                           <td>
                              <?php
                              $days = floor($items['day'] / 24);
                              $remainingHours = $items['day'] % 24;
                              $parts = [];
                              if ($days > 0) {
                                 $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                              }
                              if ($remainingHours > 0) {
                                 $parts[] = $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '');
                              }
                              echo $parts ? implode(' ', $parts) : '0 hours';
                              ?>

                           </td>
                           <td>{{ $items['percentage'] }}</td>
                           <td>{!! Str::limit($items['message'],40) !!} </td>
                           @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.tour-refund-policy.status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$items['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="items-status{{ $items['id'] }}" value="1"
                                       {{ $items['status'] == 1 ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="items-status{{ $items['id'] }}"
                                       data-on-image="items-status-on.png"
                                       data-off-image="items-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' Tour refund policy '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' Tour refund policy '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_Tour_refund_policy_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_Tour_refund_policy_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif
                           @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'edit') || Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'delete'))
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.tour-refund-policy.update',[$items['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 <a class="tour_package-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
                                    <i class="tio-delete"></i>
                                 </a>
                              </div>
                           </td>
                           @endif
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for tour package list -->
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
<span id="route-admin-tour_package-delete" data-url="{{ route('admin.tour-refund-policy.delete') }}"></span>
<!-- Toast message for tour package deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Tour_Package_package_deleted') }}
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
   $('.tour_package-delete-button').on('click', function() {
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
            // Send AJAX request to delete tour caregory
            $.ajax({
               url: $('#route-admin-tour_package-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: packageId
               },
               success: function(response) {
                  // Show success message
                  toastr.success("{{translate('Tour_refund_policy_deleted_successfully')}}", '', {
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
<script>
   function updateConvertedHours() {
      const type = document.querySelector('.hourSelect').value;
      const value = parseInt(document.querySelector('.user_enter_hours_days').value);
      let totalHours = 0;
      if (!isNaN(value)) {
         if (type === 'hours') {
            totalHours = value;
         } else if (type === 'day') {
            totalHours = value * 24;
         }
      }
      $(".convert_hours").val(totalHours);
   }
</script>
@endpush