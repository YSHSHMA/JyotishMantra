@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('tour_package'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('tour_package') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new tour_package -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-header d-block">
               <div class="float-end">
                  @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'add-hotel'))
                  <a class="btn btn-primary" data-toggle="modal" data-target="#hotelModal" onclick="$('#hotelName').val('');loadHotels()">Add hotel</a>
                  @endif
               </div>
            </div>
            <div class="card-body">
               <form action="{{ route('admin.tour_package.store') }}" method="post" enctype="multipart/form-data">
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
                     <div class="col-md-8">

                        @foreach($languages as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="{{ ((old('type','') == 'hotel' )?'col-md-4':'col-md-6') }} add-div6-class">
                                 <label class="title-color" for="type">{{ translate('select_type') }}</label>
                                 <select name="type" value="{{old('type')}}" onchange="$('.eat_foods_type').val(this.value)" class="form-control eat_foods_type" required>
                                    <option value=""> {{ translate('select_type') }}</option>
                                    <option value="foods" {{ ((old('type') == 'foods' )?'selected':'') }}>foods</option>
                                    <option value="hotel" {{ ((old('type') == 'hotel' )?'selected':'') }}>hotel</option>
                                 </select>
                              </div>
                              <div class="{{ ((old('type','') == 'hotel' )?'col-md-4':'col-md-6') }} add-div6-class">
                                 <label class="title-color" for="name">{{ translate('package_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <input type="text" name="name[]" class="form-control" value="{{old('name.'.$loop->index)}}" data-lan="{{$lang}}" placeholder="{{ translate('package_name') }}">
                              </div>
                              <div class="{{ ((old('type','') == 'hotel' )?'col-md-4':'col-md-12 d-none') }} add-div4-class">
                                 <label class="title-color" for="name">{{ translate('package_category_select') }}<span class="text-danger">*</span></label>
                                 <select class="form-control hotel_options hotel_options{{$lang}}" name="hotel_type" onclick="getOptions()" onchange=$('.hotel_options').val(this.value)>
                                    <option value="">Select Package</option>
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                 <label class="title-color" for="seat">{{ translate('seats') }}</label>
                                 <input type="number" name="seats" value="{{old('seats')}}" onkeyup="$('.seats_set').val(this.value)" class="form-control seats_set" placeholder="{{ translate('enter_cab_seat') }}" {{$lang == $defaultLanguage? 'required':''}}>
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="title_name">{{ translate('title_name') }}</label>
                                 <input type="text" name="title[]" value="{{old('title.'.$loop->index) }}" class="form-control" placeholder="{{ translate('enter_title') }}" {{$lang == 'en'? 'required':''}}>
                              </div>
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <textarea name="description[]" class="form-control ckeditor" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}>{{old('description.'.$loop->index) }}</textarea>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/def.png', type: 'backend-product')  }}"
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
                     @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'add'))
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
                           <th>{{ translate('name') }}</th>
                           <th>{{ translate('type') }}</th>
                           <th>{{ translate('no_seat') }}</th>
                           <th>{{ translate('title') }}</th>
                           <th>{{ translate('image') }}</th>
                           @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'status'))
                           <th>{{ translate('status') }}</th>
                           @endif
                           @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'edit') || Helpers::modules_permission_check('Tour', 'Tour Package', 'delete'))
                           <th>{{ translate('action') }}</th>
                           @endif
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through items -->
                        @foreach($getData as $key => $items)
                        <tr>
                           <td>{{$getData->firstItem()+$key}}</td>
                           <td>{{ $items['name'] }}</td>
                           <td>{{ ucwords($items['type']) }}</td>
                           <td>{{ $items['seats'] }}</td>
                           <td>{{ $items['title'] }}</td>
                           <td>
                              <div class="avatar-60 d-flex align-items-center rounded">
                                 <img class="img-fluid" alt="" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/' . $items['image'], type: 'backend-panchang') }}">
                              </div>
                           </td>
                           @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'status'))
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.tour_package.status-update') }}" method="post" id="items-status{{$items['id']}}-form">
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
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$items['name'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$items['name'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_tour_package_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_tour_package_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           @endif
                           @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'edit') || Helpers::modules_permission_check('Tour', 'Tour Package', 'delete'))
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.tour_package.update',[$items['id']])}}">
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
<span id="route-admin-tour_package-delete" data-url="{{ route('admin.tour_package.delete') }}"></span>
<!-- Toast message for tour package deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Tour_Package_package_deleted') }}
      </div>
   </div>
</div>

<div class="modal fade" id="hotelModal" tabindex="-1" role="dialog" aria-labelledby="hotelModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Add Hotel</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-12">
                  <input type="text" id="hotelName" class="form-control" placeholder="Enter hotel name">
               </div>
               <div class="col-12 mt-2">
                  <button type="button" id="saveHotel" class="btn btn-primary float-end">Save</button>
               </div>
               <div class="col-12 mt-2">
                  <h5>Hotel List</h5>
                  <ul id="hotelList" class="list-group">

                  </ul>
               </div>
            </div>
         </div>
         <div class="modal-footer">
         </div>
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
                  toastr.success('Tour Package deleted successfully', '', {
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
   $(document).ready(function() {
      $("#saveHotel").on("click", function() {
         let hotel = $("#hotelName").val().trim();
         if (hotel === "") {
            Swal.fire("Error", "Please enter hotel name", "error");
            return;
         }

         Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save this hotel?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
               return $.ajax({
                  url: "{{ route('admin.tour_package.add-hotel-package') }}",
                  method: "POST",
                  data: {
                     hotel_name: hotel,
                     _token: "{{ csrf_token() }}"
                  },
                  dataType: "json"
               }).then(response => {
                  if (response.success) {
                     return response;
                  } else {
                     Swal.showValidationMessage(response.message || "Save failed");
                  }
               }).catch(() => {
                  Swal.showValidationMessage("Request failed");
               });
            }
         }).then((result) => {
            if (result.isConfirmed) {
               $("#hotelModal").modal('hide');
               Swal.fire("Saved!", "Hotel has been added.", "success");
            }
         });
      });
   });

   function loadHotels() {
      $.get("{{ route('admin.tour_package.get-hotel-package') }}", function(data) {
         $("#hotelList").empty();
         data.forEach(hotel => {
            $("#hotelList").append(
               `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${hotel.name}
                        <button class="btn btn-sm btn-danger removeHotel" data-id="${hotel.id}">Remove</button>
                    </li>`
            );
         });
      });
   }
   loadHotels();
   $(document).on("click", ".removeHotel", function() {
      let id = $(this).data("id");
      let li = $(this).closest("li");

      $.ajax({
         url: "{{ route('admin.tour_package.delete-hotel-package') }}?id=" + id,
         type: "DELETE",
         data: {
            _token: "{{ csrf_token() }}"
         },
         success: function(res) {
            if (res.success) {
               li.remove();
               Swal.fire("Deleted", res.message, "success");
            }
         }
      });
   });

  function getOptions() {
    $.get("{{ route('admin.tour_package.get-hotel-package') }}", function(data) {
        let $select = $(".hotel_options");
        let oldLength = $(".hotel_optionsen").find("option").length - 1;
        let newLength = data.length;
        console.log($select);
        console.log(newLength);
        console.log(oldLength);
        if (oldLength === newLength) {
            return;
        }
        $select.empty();
        $select.append(`<option value="">Select Package</option>`);
        data.forEach(hotel => {
            $select.append(
                `<option value="${hotel.name}" data-name="${hotel.name}">${hotel.name}</option>`
            );
        });
    });
}

$(document).ready(function() {
    function toggleFields() {
        let type = $(".eat_foods_type").val();

        if(type === "foods") {
            $(".add-div6-class").removeClass("col-md-4");
            $(".add-div6-class").addClass("col-md-6");
            $(".add-div4-class").removeClass("col-md-4");
            $(".add-div4-class").addClass("col-md-12");
            $(".add-div4-class").addClass("d-none");
        } 
        else if(type === "hotel") {
            $(".add-div6-class").removeClass("col-md-6");
            $(".add-div6-class").addClass("col-md-4");
            $(".add-div4-class").removeClass("col-md-12");
            $(".add-div4-class").addClass("col-md-4");
            $(".add-div4-class").removeClass("d-none");
        } 
        
    }

    $(document).on("change", ".eat_foods_type", function() {
        toggleFields();
    });
});

</script>
@endpush