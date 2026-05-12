@extends('layouts.back-end.app-tour')

@section('title', translate('cab_list'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('cab_list') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new tour_cab -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('tour-vendor.tour_cab_management.cab-store') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <label class="title-color" for="name">{{ translate('select_cab') }}<span class="text-danger">*</span></label>
                        <select name="cab_id" class="form-control">
                           <option value="">{{ translate('select_cab') }}</option>
                           @if($cab_list)
                           @foreach($cab_list as $va)
                           <option value="{{ $va['id']}}" {{ ((old('cab_id') == $va['id'] )?"selected" :"" ) }}>{{ $va['name'] }}</option>
                           @endforeach
                           @endif
                        </select>
                     </div>
                     <div class="col-md-6">
                        <label class="title-color" for="reg_number">{{ translate('reg_number') }}</label>
                        <input type="text" name="reg_number" value="{{old('reg_number')}}" class="form-control" placeholder="{{ translate('enter_register_number') }}" required>
                     </div>
                     <div class="col-md-6">
                        <label class="title-color" for="model_number">{{ translate('model_number') }}</label>
                        <input type="text" name="model_number" value="{{old('model_number') }}" class="form-control" placeholder="{{ translate('enter_model_number') }}" required>
                     </div>
                     <div class="col-md-6">
                        <label class="title-color" for="fuel_type">{{ translate('fuel_type') }}</label>
                        <select name="fuel_type" class="form-control" required>
                           <option value="">Select Fuel Type</option>
                           <option value="petrol" {{ (('petrol' == old('fuel_type') )?'selected':'')}}>Petrol</option>
                           <option value="diesel" {{ (('diesel' == old('fuel_type') )?'selected':'')}}>Diesel</option>
                           <option value="cng" {{ (('cng' == old('fuel_type') )?'selected':'')}}>CNG</option>
                           <option value="electric" {{ (('electric' == old('fuel_type') )?'selected':'')}}>Electric</option>
                           <option value="hybrid" {{ (('hybrid' == old('fuel_type') )?'selected':'')}}>Hybrid</option>
                        </select>
                     </div>
                     <div class="additional_image_column col-md-12 mt-2">
                        <div class="card h-100">
                           <div class="card-body">
                              <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                 <div>
                                    <label for="name"
                                       class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                    <span
                                       class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                       title="{{ translate('upload_any_additional_images_for_this_vehicle_from_here') }}.">
                                       <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                          alt="">
                                    </span>
                                 </div>
                              </div>
                              <p class="text-muted">{{ translate('upload_additional_vehicle_images') }}</p>
                              <div class="row g-2" id="additional_Image_Section">
                                 <div class="col-sm-12 col-md-4">
                                    <div class="custom_upload_input position-relative border-dashed-2">
                                       <input type="file" name="image[]" class="custom-upload-input-file action-add-more-image" data-index="1" data-imgpreview="additional_Image_1" accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*" data-target-section="#additional_Image_Section">

                                       <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                          <i class="tio-delete"></i>
                                       </span>

                                       <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                          <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">
                                       </div>
                                       <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                          <div class="d-flex flex-column justify-content-center align-items-center">
                                             <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
                                             <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end mt-2">
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
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
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $getData ? $getData->total() ?? '' : '' }}</span>
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
                           <th>{{ translate('cab_name') }}</th>
                           <th>{{ translate('reg_number') }}</th>
                           <th>{{ translate('model_name') }}</th>
                           <th>{{ translate('fuel_type') }}</th>
                           <th>{{ translate('status') }}</th>
                           <th>{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through items -->
                        @foreach($getData as $key => $items)
                        <tr>
                           <td>{{$getData->firstItem()+$key}}</td>
                           <td>{{ ($items['Cabs']['name']??"") }}</td>
                           <td>{{ $items['reg_number'] }}</td>
                           <td>{{ $items['model_number'] }}</td>
                           <td>
                              {{ $items['fuel_type'] }}
                           </td>
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('tour-vendor.tour_cab_management.cab_status-update') }}" method="post" id="items-status{{$items['id']}}-form">
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
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.($items['Cabs']['name']??'').' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.($items['Cabs']['name']??'').' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_tour_traveller_cab_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_tour_traveller_cab_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('tour-vendor.tour_cab_management.cab-update',[$items['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 <a class="tour_package-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
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
<span id="route-admin-tour_package-delete" data-url="{{ route('tour-vendor.tour_cab_management.traveller-cab-delete') }}"></span>
<!-- Toast message for tour package deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Tour_traveller_cab_deleted') }}
      </div>
   </div>
</div>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
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
                  toastr.success("{{translate('Tour_traveller_cab_deleted')}}", '', {
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