@extends('layouts.back-end.app')
@section('title', translate('temple_package_master_price'))
@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
  .gj-timepicker-bootstrap [role=right-icon] button .gj-icon { top: 14px; right: 5px; }
  .search-filter { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
            {{ translate('temple_package_master_price') }}
        </h2>
    </div>

    <div class="search-filter">
        <input type="text" id="searchInput" class="form-control" placeholder="Search Temple / Service">
        <select id="availabilityFilter" class="form-control">
            <option value="">All Availability</option>
            <option value="1">Available</option>
            <option value="0">Not Available</option>
        </select>
    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <div class="accordion" id="templeAccordion">
                @foreach($temples as $temple)
                    <div class="card mb-2">
                    <div class="card-header d-flex justify-content-between align-items-center" id="heading-{{ $temple->id }}">
                        <!-- Left Side -->
                        <h5 class="mb-0">{{ $temple->name }}</h5>

                        <!-- Right Side Buttons -->
                        <div class="btn-group">
                            
                            <button class="btn btn-primary btn-sm" type="button"   
                                data-toggle="collapse" 
                                data-target="#collapse-{{ $temple->id }}"  
                                aria-expanded="false"  
                                aria-controls="collapse-{{ $temple->id }}">
                                <i class="tio-book"></i>
                            </button>
                        </div>
                    </div>


                        <div id="collapse-{{ $temple->id }}" class="collapse" data-bs-parent="#templeAccordion">
                            <div class="card-body">
                                @if($temple->servicePrices->count())
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Variant Name</th>
                                            <th>Base Price</th>
                                            <th>GST Rate</th>
                                            <th>Is Available</th>
                                            <th>Slots</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($temple->servicePrices as $price)
                                        <tr>
                                            <td>{{ $price->servicePackage->name ?? 'N/A' }}</td>
                                            <td>{{ $price->varient_name }}</td>
                                            <td>{{ $price->base_price }}</td>
                                            <td>{{ $price->gst_rate }}%</td>
                                            <td>{{ $price->is_available ? 'Yes' : 'No' }}</td>
                                            <td>
                                                @if($price->slots->count())
                                                <ul class="mb-0">
                                                    @foreach($price->slots as $slot)
                                                    <li>
                                                        <strong>{{ ucfirst($slot->day_of_week) }}</strong> |
                                                        {{ $slot->start_time }} - {{ $slot->end_time }} |
                                                        Slots: {{ $slot->slots_limi_capacity }}
                                                    </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                No Slots
                                                @endif
                                            </td>

                                            <td>
                                                <form action="{{ route('admin.temple.templevariantstatus', ['id' => $price['id']]) }}"        method="post" id="variant-status{{$price['id']}}-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{$price['id']}}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                                    id="variant-status{{ $price['id'] }}" value="1" {{ $price['status'] == 1 ? 'checked' : '' }}
                                                                    data-modal-id = "toggle-status-modal"
                                                                    data-toggle-id = "variant-status{{ $price['id'] }}"
                                                                    data-on-image = "variant-status-on.png"
                                                                    data-off-image = "variant-status-off.png"
                                                                    data-on-title = "{{ translate('Want_to_Turn_ON').' '.$price['defaultname'].' '. translate('status') }}"
                                                                    data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$price['defaultname'].' '.translate('status') }}"
                                                                    data-on-message = "<p>{{ translate('if_enabled_this_variant_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                    data-off-message = "<p>{{ translate('if_disabled_this_variant_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                </form>
                                            </td>
                                            <td>
                                                
                                            <a href="{{ route('admin.temple.packageeditprice', [$price->id, $temple->id]) }}" 
                                                class="btn btn-primary btn-sm" id="{{ $price->id }}">
                                                    <i class="tio-edit"></i>
                                                </a>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                    <p>No packages found for this temple.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const availabilityFilter = document.getElementById("availabilityFilter");
    const accordion = document.getElementById("templeAccordion");

    function filterAccordion() {
        const searchValue = searchInput.value.trim().toLowerCase();
        const availabilityValue = availabilityFilter.value;
        Array.from(accordion.querySelectorAll('.card')).forEach(card => {
            const templeName = card.querySelector('.card-header h5').innerText.toLowerCase();
            let matchAvailable = false;
            card.querySelectorAll('tbody tr').forEach(row => {
                const isAvailable = row.cells[4].innerText.trim() === 'Yes' ? '1' : '0';
                if (!availabilityValue || availabilityValue === isAvailable) {
                    matchAvailable = true;
                }
            });
            const matchesSearch = searchValue === "" || templeName.includes(searchValue);
            if (matchesSearch && matchAvailable) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    searchInput.addEventListener("keyup", filterAccordion);
    availabilityFilter.addEventListener("change", filterAccordion);

    filterAccordion();
});
</script>

@endpush
