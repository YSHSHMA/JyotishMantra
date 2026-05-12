@extends('layouts.back-end.app')
@section('title', translate('temple_package_master'))
@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
   .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
   top: 14px;
   right: 5px;
   }
</style>
@endpush
@section('content')
<div class="content container-fluid">
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
         <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
            {{ translate('temple_package_master') }}
         </h2>
      </div>
      <form action="{{ route('admin.temple.savepackage_services', $temple->id) }}" method="POST">
         @csrf
            @php
               // Backend से selectedServices JSON से extract ids
               $selectedIds = $selectedServices ? array_column($selectedServices, 'id') : [];
            @endphp

         <div class="card p-3 mb-3">
            <h5>{{ translate('Select Services') }}</h5>
            <div class="mb-2 d-flex gap-2"> <!-- flex + gap -->
               <button type="button" class="btn btn-md btn-outline-primary" id="select-all">
                  {{ translate('Select All') }}
               </button>
               <button type="button" class="btn btn-md btn-outline-danger" id="deselect-all">
                  {{ translate('Deselect All') }}
               </button>
            </div>
            <div class="row">
               @foreach($allServices as $service)
                     <div class="col-md-3 col-sm-4 col-6 mb-2">
                        <div class="form-check">
                           <input class="form-check-input package-checkbox" type="checkbox" 
                                    name="package_service[]" 
                                    value="{{ $service->id }}"
                                    {{ in_array($service->id, $selectedIds) ? 'checked' : '' }}>
                           <label class="form-check-label">{{ ucfirst($service->name) }}</label>
                        </div>
                     </div>
               @endforeach
            </div>
            <button type="submit" class="btn btn-success mt-3">{{ translate('Save Services') }}</button>
         </div>
      </form>
      <form class="product-form" action="{{ route('admin.temple.storePackagePrice') }}" method="post" enctype="multipart/form-data">
         @csrf
         <input type="hidden" name="id" value="{{ $editPackage->id ?? '' }}">
         <div class="card p-3 mb-3">
            <h5>{{ translate('select_service_for_package') }}</h5>
            <div class="mb-2 d-flex gap-2">
               <div class="col-md-12 col-lg-12">
                  <div class="form-group">
                  
                     <select name="package_id" id="idPackage" class="form-control" required>
                        @foreach($filteredServices as $service)
                           <option value="{{ $service->id }}"  {{-- value=id --}}
                                 {{ in_array($service->id, collect($selectedServices)->pluck('id')->toArray()) ? 'selected' : '' }}>
                                 {{ $service->name }} {{-- show name --}}
                           </option>
                        @endforeach
                     </select>
                  </div>
               </div>
            </div>
         </div>
         <div class="card">
            <div class="px-4 pt-3">
               <ul class="nav nav-tabs w-fit-content mb-4">
                  @foreach ($languages as $lang)
                  <li class="nav-item">
                     <span
                        class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                        id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                  </li>
                  @endforeach
               </ul>
            </div>
            <div class="card-body">
               @foreach ($languages as $lang)
               <?php
                  $translate = [];
                  if (!empty($editPackage?->translations)) {
                     foreach ($editPackage->translations as $translation) {
                        if ($translation->locale == $lang && $translation->key == 'name') {
                              $translate[$lang]['name'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'short_description') {
                              $translate[$lang]['short_description'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'type') {
                              $translate[$lang]['type'] = $translation->value;
                        }
                     }
                  }
                  ?>
               <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                  id="{{ $lang }}-form">
                  <input type="hidden" name="lang[]" value="{{ $lang }}">
                  <input type="hidden" name="temple_id" value="{{ $temple->id }}">
                  <input type="hidden" name="trust_id" value="{{ $temple->trust_id }}">
                  <div class="row">
                     {{-- Name --}}
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="title-color" for="{{ $lang }}_name">
                           {{ translate('variant_name') }} ({{ strtoupper($lang) }})
                           </label>
                           <input type="text" name="name[]"  id="{{ $lang }}_name"  class="form-control" placeholder="{{ translate('temple_pacakge_name') }} ({{ strtoupper($lang) }})"
                           value="{{ $translate[$lang]['name'] ?? $editPackage?->name ?? '' }}" {{ $lang == $defaultLanguage ? 'required' : '' }}>
                        </div>
                     </div>
                     {{-- Short Description --}}
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="title-color" for="{{ $lang }}_details">{{ translate('description') }}
                           ({{ strtoupper($lang) }})</label>
                           <textarea class="ckeditor" id="editor{{ $lang }}" name="details[]" required></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
         <div class="card mt-3 rest-part">
            <div class="card-header">
               <div class="d-flex gap-2">
                  <i class="tio-user-big"></i>
                  <h4 class="mb-0">{{ translate('Settings_for_price') }}</h4>
               </div>
            </div>
            <div class="card-body">
               <div class="row">
                 
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="name" class="title-color"> {{ translate('Image') }}<span class="text-danger">*</span></label>
                           <span class="ml-1 text-info">{{ THEME_RATIO[theme_root_path()]['Brand Image'] ?? '' }}
                        </span>

                           <div class="custom-file text-left">
                              <input type="file" name="image" id="package-image" class="custom-file-input image-preview-before-upload" data-preview="#viewer" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="package-image">{{translate('choose_file') }}</label>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="name" class="title-color"> {{ translate('Color') }}<span class="text-danger">*</span></label>
                           <div class="custom-file text-left">
                              <input type="color" name="color" id="color" class="form-control" required>
                           </div>
                        </div>
                     </div>
                     
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('daily_slots_limit') }}</label>
                           <input type="number" name="daily_slots_limit" class="form-control"
                              placeholder="{{ translate('daily_slots_limit') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('max_qty_per_day') }}</label>
                           <input type="number" name="max_qty_per_day" class="form-control" placeholder="{{ translate('max_qty_per_day') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('max_duration_hour') }}</label>
                           <input type="number" name="max_duration_hour" class="form-control"
                              placeholder="{{ translate('max_duration_hour') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('base_price') }}</label>
                           <input type="number" name="base_price" class="form-control" placeholder="{{ translate('base_price') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('platform_fee_percentage') }}</label>
                           <input type="number" name="platform_fee_percentage" class="form-control" placeholder="{{ translate('platform_fee_percentage') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('receipt_fee_percentage') }}</label>
                           <input type="number" name="receipt_fee_percentage" class="form-control"
                              placeholder="{{ translate('receipt_fee_percentage') }}">
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('gst_rate') }}</label>
                           <input type="number" name="gst_rate" class="form-control" placeholder="{{ translate('gst_rate') }}" value="{{ $gstRate }}" readonly>
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                           <label class="title-color">{{ translate('is_available') }}({{ translate('time_slot_book') }})</label>
                           <select name="is_available" id="isAvailable" class="form-control">
                              <option value="1">True</option>
                              <option value="0">False</option>
                        </select>
                        </div>
                     </div>
                  </div>
                  <div class="card mt-3" id="typeSlots" style="display:none;">
                     <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                              <i class="tio-user-big"></i>
                              <h4 class="mb-0">{{ translate('slot_for_the_time') }}</h4>
                        </div>
                        <button type="button" class="btn btn-md btn-success" id="addWeek"><i class="tio-plus"></i> Add Week</button>
                     </div>

                     <div class="card-body" id="weekContainer">
                        <!-- Week group template -->
                        <div class="weekGroup border p-3 mb-3">
                           <div class="col-md-12">
                              <div class="d-flex justify-content-between align-items-center mb-2">
                                 <div class="form-group mb-0 w-50">
                                    <label>{{ translate('select_week') }}</label>
                                    <select name="day_of_week[]" class="form-control">
                                          <option value="">--Select Week Day--</option>
                                          <option value="sunday">Sunday</option>
                                          <option value="monday">Monday</option>
                                          <option value="tuesday">Tuesday</option>
                                          <option value="wednesday">Wednesday</option>
                                          <option value="thursday">Thursday</option>
                                          <option value="friday">Friday</option>
                                          <option value="saturday">Saturday</option>
                                    </select>
                                 </div>
                                 <button type="button" class="btn btn-danger btn-md removeWeek"><i class="tio-delete"></i></button>
                              </div>
                           

                              <!-- Time slots container -->
                              <div class="timeSlotContainer">
                                 <div class="row timeRow mb-2">
                                    <div class="col-md-3">
                                          <label>{{ translate('start_time') }}</label>
                                          <input type="time" name="start_time[0][]" class="form-control" placeholder="Start Time">
                                    </div>
                                    <div class="col-md-3">
                                          <label>{{ translate('end_time') }}</label>
                                          <input type="time" name="end_time[0][]" class="form-control" placeholder="End Time">
                                    </div>
                                    <div class="col-md-3">
                                          <label>{{ translate('slot_imit') }}</label>
                                          <input type="number" name="slots_limi_capacity[0][]" class="form-control" placeholder="Capacity">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                          <button type="button" class="btn btn-danger btn-md removeTime"><i class="tio-delete"></i></button>
                                    </div>
                                 </div>
                              </div>
                              <button type="button" class="btn btn-md btn-primary addTime">+ Add Time Slot</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row justify-content-end gap-3 mt-3 mx-1">
                  <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                  <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
               </div>
            </div>
         </div>
      </form>
</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
{{-- ck editor --}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
{{-- JS for select/deselect all --}}

<script>
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.package-checkbox').forEach(el => el.checked = true);
});
document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.package-checkbox').forEach(el => el.checked = false);
});
</script>
<script>
   document.addEventListener("DOMContentLoaded", function () {
      const select = document.getElementById("isAvailable");
      const typeSlots = document.getElementById("typeSlots");
      const weekContainer = document.getElementById("weekContainer");
      const addWeekBtn = document.getElementById("addWeek");

      // show/hide slots
      function toggleSlots() {
         typeSlots.style.display = (select.value === "1") ? "block" : "none";
      }
      toggleSlots();
      select.addEventListener("change", toggleSlots);

      // update already selected weeks → disable in other dropdowns
      function updateWeekOptions() {
         let selectedWeeks = Array.from(weekContainer.querySelectorAll("select[name='day_of_week[]']"))
               .map(sel => sel.value).filter(v => v);

         weekContainer.querySelectorAll("select[name='day_of_week[]']").forEach(sel => {
               sel.querySelectorAll("option").forEach(opt => {
                  opt.disabled = (opt.value && selectedWeeks.includes(opt.value) && sel.value !== opt.value);
               });
         });
      }

      // reindex week & time inputs properly
      function reIndexWeeks() {
         weekContainer.querySelectorAll(".weekGroup").forEach((group, wIndex) => {
               group.querySelectorAll(".timeRow").forEach(row => {
                  row.querySelectorAll("input").forEach(input => {
                     if (input.name.includes("start_time")) input.name = `start_time[${wIndex}][]`;
                     if (input.name.includes("end_time")) input.name = `end_time[${wIndex}][]`;
                     if (input.name.includes("slots_limi_capacity")) input.name = `slots_limi_capacity[${wIndex}][]`;
                  });
               });
         });
      }

      // Add new week
      addWeekBtn.addEventListener("click", function () {
         let template = document.querySelector(".weekGroup");
         let newWeek = template.cloneNode(true);

         // reset selects & inputs
         newWeek.querySelector("select").value = "";
         newWeek.querySelectorAll("input").forEach(el => el.value = "");

         weekContainer.appendChild(newWeek);
         reIndexWeeks();
         updateWeekOptions();
      });

      // Handle buttons inside container
      weekContainer.addEventListener("click", function (e) {
         if (e.target.classList.contains("removeWeek")) {
               if (weekContainer.querySelectorAll(".weekGroup").length > 1) {
                  e.target.closest(".weekGroup").remove();
                  reIndexWeeks();
                  updateWeekOptions();
               } else {
                  alert("At least one week is required.");
               }
         }

         if (e.target.classList.contains("addTime")) {
               let weekGroup = e.target.closest(".weekGroup");
               let timeRow = weekGroup.querySelector(".timeRow").cloneNode(true);
               timeRow.querySelectorAll("input").forEach(el => el.value = "");
               weekGroup.querySelector(".timeSlotContainer").appendChild(timeRow);
               reIndexWeeks();
         }

         if (e.target.classList.contains("removeTime")) {
               let container = e.target.closest(".timeSlotContainer");
               if (container.querySelectorAll(".timeRow").length > 1) {
                  e.target.closest(".timeRow").remove();
                  reIndexWeeks();
               } else {
                  alert("At least one time slot per week required.");
               }
         }
      });

      // Week change → update options
      weekContainer.addEventListener("change", function (e) {
         if (e.target.name === "day_of_week[]") updateWeekOptions();
      });

      // init
      reIndexWeeks();
      updateWeekOptions();
   });
</script>


@endpush