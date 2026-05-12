@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('add_package'))

@section('content')
    <style>
        .switcher_control {
            pointer-events: none !important;
        }
    </style>
    {{-- main page --}}
    <div class="content container-fluid">
        <div class="row mt-20">
            <div class="col-md-12">
                {{-- information --}}
                <div class="card card-top-bg-element mb-5">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-3 justify-content-between">
                            <div class="media flex-column flex-sm-row gap-3">
                                <img class="avatar avatar-170 rounded-0" src="{{ $overview['image'] }}"
                                    alt="{{ translate('image') }}">
                                <div class="media-body">
                                    <div class="d-block">
                                        <h2 class="mb-2 pb-1">
                                            {{ @ucwords($overview['name']) . ' - (' . @ucwords($overview['type']) . ')' }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-2">
            <div class="col-md-12">
              
                <div class="row">

                    <div class="col-12">
                        <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                            <ul class="nav nav-tabs flex-wrap page-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active"
                                        href="{{ route('admin.astrologers.manage.add-package', $overview['id']) }}">Package</a>
                                </li>
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-detail', $overview['id']) }}">Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-additional-detail', $overview['id']) }}">Additional Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-gallery', $overview['id']) }}">Gallery</a>
                                </li>
                                {{-- @endif --}}
                                @if (!empty($overview['consultation_charge']))
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-counselling', $overview['id']) }}">Counselling</a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-content mt-5">
                            <div class="tab-pane fade show active" id="order">
                                <div class="row pt-2">
                                    <div class="col-md-12">
                                        <div class="card w-100">
                                            <div class="card-header">
                                                <h5 class="mb-0">Service Packages</h5>
                                            </div>
                                            <div class="col-12">
                                                <form action="{{ route('admin.astrologers.manage.store-package') }}"
                                                    method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="pandit_id" value="{{ $overview['id'] }}">
                                                    <div class="table-responsive datatable-custom">
                                                        <table id="datatable" style="text-align: left;"
                                                            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                            <thead class="thead-light thead-50 text-capitalize">
                                                                <tr>
                                                                    <th style="width: 5%;">{{ translate('#') }}</th>
                                                                    <th style="width: 20%;">{{ translate('name') }}</th>
                                                                    <th style="width: 25%;">{{ translate('thumbnail') }}</th>
                                                                    <th style="width: 25%;">{{ translate('package') }}</th>
                                                                    <th style="width: 15%;">{{ translate('price') }}</th>
                                                                    <th style="width: 10%;">{{ translate('status') }}</th>
                                                                    <th class="text-left">{{ translate('action') }}</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="set-rows">

                                                                 @foreach ($services as $serviceIndex => $service)
                                                                    @php
                                                                    $savedRows = $groupedPackages[$service->id] ?? collect();
                                                                    @endphp

                                                                    {{-- ===================== SAVED ROWS ===================== --}}
                                                                    @foreach ($savedRows as $index => $saved)
                                                                        <tr class="service-row"
                                                                            data-service="{{ $service->id }}">

                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">
                                                                            <input type="hidden" name="row_id[]"
                                                                                value="{{ $saved->id }}">
                                                                            {{-- IMPORTANT FOR UPDATE --}}

                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    {{ $loop->parent->iteration }})
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    {{ $service->name }}
                                                                                @endif
                                                                            </td>

                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    <div class="pb-2 text-center">
                                                                                        <img src="{{url('/storage/app/public/astrologers/service-thumbnail/'.$saved->thumbnail)}}" alt="" width="50">
                                                                                    </div>
                                                                                    <label for="" class="form-label">Update</label>
                                                                                    <input type="hidden" name="thumbnail[]" class="form-control">
                                                                                    <input type="file" name="thumbnail[]"
                                                                                        class="form-control" accept="image/*">
                                                                                @endif
                                                                            </td>

                                                                            <td>
                                                                                <select name="package_id[]"
                                                                                    class="form-control package-select">
                                                                                    @foreach ($packages as $package)
                                                                                        <option value="{{ $package->id }}"
                                                                                            {{ $saved->package_id == $package->id ? 'selected' : '' }}>
                                                                                            {{ $package->title }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="price[]"
                                                                                    class="form-control price-input"
                                                                                    value="{{ $saved->price }}" required>
                                                                            </td>

                                                                            <td>
                                                                                <label class="switcher mx-auto">
                                                                                    <input type="checkbox"
                                                                                        class="switcher_input"
                                                                                        name="status[]" value="1"
                                                                                        {{ $saved->status == 1 ? 'checked' : '' }}>
                                                                                    <span class="switcher_control"></span>
                                                                                </label>

                                                                                <input type="hidden" name="status_hidden[]"
                                                                                    value="{{ $saved->status }}">
                                                                            </td>

                                                                            <td>
                                                                                @if ($index == 0)
                                                                                    <button type="button"
                                                                                        class="btn btn-success add-package"><i>+</i></button>
                                                                                @else
                                                                                    <button type="button"
                                                                                        class="btn btn-danger delete-package"><i>-</i></button>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach

                                                                    {{-- ===================== EMPTY ROW FOR NEW PACKAGE ===================== --}}
                                                                    @if (count($savedRows) == 0)
                                                                        <tr class="service-row"
                                                                            data-service="{{ $service->id }}">
                                                                            <input type="hidden" name="service_id[]"
                                                                                value="{{ $service->id }}">
                                                                            <input type="hidden" name="row_id[]"
                                                                                value="">

                                                                            <td>
                                                                                {{ $loop->iteration }})
                                                                            </td>

                                                                            <td>
                                                                                {{ $service->name }}
                                                                            </td>

                                                                            <td>
                                                                                <input type="file" name="thumbnail[]"
                                                                                    class="form-control" accept="image/*">
                                                                            </td>

                                                                            <td>
                                                                                <select name="package_id[]"
                                                                                    class="form-control package-select">
                                                                                    <option value="">-- Select Package
                                                                                        --</option>
                                                                                    @foreach ($packages as $package)
                                                                                        <option
                                                                                            value="{{ $package->id }}">
                                                                                            {{ $package->title }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </td>

                                                                            <td>
                                                                                <input type="number" name="price[]"
                                                                                    class="form-control price-input"
                                                                                    value="0" required>
                                                                            </td>

                                                                            <td>
                                                                                <label class="switcher mx-auto">
                                                                                    <input type="checkbox"
                                                                                        class="switcher_input "
                                                                                        name="status[]" value="1"
                                                                                        checked>
                                                                                    <span class="switcher_control"></span>
                                                                                </label>

                                                                                <input type="hidden"
                                                                                    name="status_hidden[]" value="1">
                                                                            </td>

                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-success add-package"><i>+</i></button>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach

                                                            </tbody>



                                                        </table>
                                                    </div>
                                                    <div class="text-center py-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            let totalPackages = {{ count($packages) }};

            // Add New Row
            $(document).on('click', '.add-package', function() {

                let parentRow = $(this).closest('tr');
                let serviceId = parentRow.data('service');

                // All rows for this service
                let serviceRows = $(`tr[data-service="${serviceId}"]`);
                let currentRows = serviceRows.length;

                // --- New validation: require last row package to be selected before adding ---
                let lastRow = serviceRows.last();
                let lastSelect = lastRow.find('.package-select');

                // If there's a lastRow and its select exists, require a selection
                if (lastSelect.length) {
                    let lastVal = lastSelect.val();
                    if (!lastVal || lastVal === "") {
                        // show error (you can replace alert with toast)
                        toastr.error("Please select a package first.");
                        // visual feedback
                        lastSelect.addClass('is-invalid');
                        lastSelect.focus();
                        return; // prevent adding new row
                    }
                }
                // ---------------------------------------------------------------------------

                if (currentRows >= totalPackages) {
                    toastr.error("You cannot add more packages.");
                    return;
                }

                // new row with EMPTY default option
                let newRow = `
                    <tr class="service-row" data-service="${serviceId}">
                        <input type="hidden" name="service_id[]" value="${serviceId}">
                        <input type="hidden" name="row_id[]" value="">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <select name="package_id[]" class="form-control package-select">
                                <option value="">-- Select Package --</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package['id'] }}">{{ $package['title'] }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="price[]" class="form-control price-input" value="0" required>
                        </td>
                        <td>
                            <label class="switcher mx-auto">
                                <input type="checkbox" class="switcher_input" name="status[]" value="1" checked>
                                <span class="switcher_control"></span>
                            </label>

                            <input type="hidden" name="status_hidden[]" value="1">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete-package"><i>-</i></button>
                        </td>
                    </tr>
                `;

                // append under last service row
                serviceRows.last().after(newRow);

                // 1️⃣ disable already selected
                updatePackageOptions(serviceId);

                // 2️⃣ auto-select the first available option
                autoSelectFirstAvailable(serviceRows.last().next(), serviceId);
            });

            // Delete Row
            $(document).on('click', '.delete-package', function() {
                let serviceId = $(this).closest('tr').data('service');
                $(this).closest('tr').remove();
                updatePackageOptions(serviceId);
            });

            // On dropdown change refresh disabled state
            $(document).on('change', '.package-select', function() {
                if ($(this).val() && $(this).val() !== "") {
                    $(this).removeClass('is-invalid');
                }
                let serviceId = $(this).closest('tr').data('service');
                updatePackageOptions(serviceId);
            });

            // Disable selected packages for this particular service
            function updatePackageOptions(serviceId) {

                let rows = $(`tr[data-service="${serviceId}"]`);
                let selectedPackages = [];

                // Collect selected packages
                rows.each(function() {
                    let val = $(this).find('.package-select').val();
                    if (val) selectedPackages.push(val);
                });

                // Disable selected options in all other rows
                rows.each(function() {

                    let select = $(this).find('.package-select');
                    let currentValue = select.val();

                    select.find("option").each(function() {
                        let optVal = $(this).val();

                        if (optVal != currentValue && selectedPackages.includes(optVal)) {
                            $(this).prop("disabled", true);
                        } else {
                            $(this).prop("disabled", false);
                        }
                    });

                });

            }

            // Auto-select first non-disabled option
            function autoSelectFirstAvailable(row, serviceId) {

                let select = row.find(".package-select");
                let firstAvailable = select.find("option:not([disabled])").first().val();

                if (firstAvailable) {
                    select.val(firstAvailable);
                    updatePackageOptions(serviceId);
                }
            }

            $("tr.service-row").each(function() {
                let serviceId = $(this).data("service");
                updatePackageOptions(serviceId);
            });

        });
    </script>

    <script>
        // service checkbox
        $(document).on("change", ".switcher_input", function() {

            let hidden = $(this)
                .closest("td")
                .find('input[name="status_hidden[]"]');

            if (!hidden.length) {
                hidden = $(this).parent().parent().find('input[name="status_hidden[]"]');
            }

            hidden.val($(this).is(":checked") ? 1 : 0);
        });
    </script>
@endpush
