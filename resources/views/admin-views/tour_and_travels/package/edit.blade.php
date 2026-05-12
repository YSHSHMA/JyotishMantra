@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_package'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_package') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new Edit_package -->
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
               <form action="{{ route('admin.tour_package.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
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

                        <!-- Input fields for event package name -->
                        @foreach($languages as $lang)
                        <?php
                        if (count($getData['translations'])) {
                           $translate = [];
                           foreach ($getData['translations'] as $translations) {
                              if ($translations->locale == $lang && $translations->key == 'name') {
                                 $translate[$lang]['name'] = $translations->value;
                              }
                              if ($translations->locale == $lang && $translations->key == 'title') {
                                 $translate[$lang]['title'] = $translations->value;
                              }
                              if ($translations->locale == $lang && $translations->key == 'description') {
                                 $translate[$lang]['description'] = $translations->value;
                              }
                           }
                        }
                        ?>
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="{{ ((old('type',$getData['type']) == 'hotel' )?'col-md-4':'col-md-6') }} add-div6-class">
                                 <label class="title-color" for="type">{{ translate('select_type') }}</label>
                                 <select name="type" onchange="$('.eat_foods_type').val(this.value)" class="form-control eat_foods_type" required>
                                    <option value=""> {{ translate('select_type') }}</option>
                                    <option value="foods" {{ ((old('type',$getData['type']) == 'foods' )?'selected':'') }}>foods</option>
                                    <option value="hotel" {{ ((old('type',$getData['type']) == 'hotel' )?'selected':'') }}>hotel</option>
                                 </select>
                              </div>
                              <div class="{{ ((old('type',$getData['type']) == 'hotel' )?'col-md-4':'col-md-6') }} add-div6-class">
                                 <label class="title-color" for="name">{{ translate('package_name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <input type="text" name="name[]" class="form-control" id="name" value="{{ $lang == $defaultLanguage ? $getData['name'] : $translate[$lang]['name'] ?? '' }}" placeholder="{{ translate('enter_package_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="{{ ((old('type',$getData['type']) == 'hotel' )?'col-md-4':'col-md-12 d-none') }} add-div4-class">
                                 <label class="title-color" for="name">{{ translate('package_category_select') }}<span class="text-danger">*</span></label>
                                 <select class="form-control hotel_options hotel_options{{$lang}}" name="hotel_type" data-selected="{{old('hotel_type',$getData['hotel_type'])}}" onclick="getOptions()" onchange=$('.hotel_options').val(this.value)>
                                    <option value="">Select Package</option>
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="seat">{{ translate('seats') }}</label>
                                 <input type="number" name="seats" value="{{old('seats',$getData['seats'])}}" onkeyup="$('.seats_set').val(this.value)" class="form-control seats_set" placeholder="{{ translate('enter_cab_seat') }}" {{$lang == 'en'? 'required':''}}>
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="title_name">{{ translate('title_name') }}</label>
                                 <input type="text" name="title[]" value="{{old('title.'.$loop->index,($translate[$lang]['title']??$getData['title'])) }}" class="form-control" placeholder="{{ translate('enter_title') }}" {{$lang == 'en'? 'required':''}}>
                              </div>
                              <div class="col-md-12">
                                 <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span>({{ strtoupper($lang) }})</label>
                                 <textarea name="description[]" class="form-control ckeditor" id="name" placeholder="{{ translate('description') }}" {{$lang == $defaultLanguage? 'required':''}}>{{ $lang == $defaultLanguage ? $getData['description'] : $translate[$lang]['description'] ?? '' }}</textarea>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/'.$getData['image'], type: 'backend-product')  }}"
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
                                 accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <input type="hidden" name="id" value="{{ $getData['id']}}">
                     @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'edit'))
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                     @endif
                  </div>
               </form>
            </div>
         </div>
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
getOptions();
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
         let selected = $select.data('selected');
         data.forEach(hotel => {
            $select.append(
               `<option value="${hotel.name}" data-name="${hotel.name}">${hotel.name}</option>`
            );
         });
         if (selected) {
            $select.val(selected);
        }
      });
   }

   $(document).ready(function() {
      function toggleFields() {
         let type = $(".eat_foods_type").val();

         if (type === "foods") {
            $(".add-div6-class").removeClass("col-md-4");
            $(".add-div6-class").addClass("col-md-6");
            $(".add-div4-class").removeClass("col-md-4");
            $(".add-div4-class").addClass("col-md-12");
            $(".add-div4-class").addClass("d-none");
         } else if (type === "hotel") {
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