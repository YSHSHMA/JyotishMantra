@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('additional_detail'))

@section('content')
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
                                    <a class="nav-link"
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
                                    <a class="nav-link active"
                                        href="{{ route('admin.astrologers.manage.add-additional-detail', $overview['id']) }}">Additional
                                        Detail</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-gallery', $overview['id']) }}">Gallery</a>
                                </li>
                                {{-- @endif --}}
                                {{-- @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order')) --}}
                                @if (!empty($overview['consultation_charge']))
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.astrologers.manage.add-counselling', $overview['id']) }}">Counselling</a>
                                </li>
                                @endif
                                {{-- @endif --}}
                            </ul>
                        </div>

                        <div class="tab-content mt-5">
                            <div class="tab-pane fade show active">
                                <div class="row pt-2">
                                    <div class="col-md-12">
                                        <div class="card w-100 mb-3">
                                            <div class="card-header">
                                                <h5 class="mb-0">Vendor Detail</h5>
                                            </div>
                                            <div class="col-md-12 py-4">
                                                <div class="row">
                                                    @if (!empty($overview->vendor_id))
                                                        <div class="col-md-8">
                                                            <div class="d-flex align-items-baseline gap-5">
                                                                @php
                                                                    $vendorId = json_decode($overview->vendor_id, true);
                                                                    $vendorDetail = App\Models\Seller::where(
                                                                        'id',
                                                                        $vendorId['id'],
                                                                    )->first();
                                                                @endphp

                                                                <img src="{{ url('/storage/app/public/shop/' . $vendorDetail->image) }}"
                                                                    alt="" width="100px">
                                                                <p>{{ $vendorDetail->f_name . ' ' . $vendorDetail->l_name }}
                                                                </p>
                                                                <label class="switcher">
                                                                    <input type="checkbox" class="switcher_input"
                                                                        id="vendor-status"
                                                                        {{ $vendorId['status'] == 1 ? 'checked' : '' }}>
                                                                    <span class="switcher_control"></span>
                                                                </label>
                                                                <form
                                                                    action="{{ route('admin.astrologers.manage.store-additional-detail') }}"
                                                                    method="post" id="vendor-status-form">
                                                                    @csrf
                                                                    <input type="hidden" name="pandit_id"
                                                                        value="{{ $overview['id'] }}">
                                                                    <input type="hidden" name="type" value="vendor">
                                                                    <input type="hidden" name="vendor_id"
                                                                        value="{{ $vendorId['id'] }}">
                                                                    <input type="hidden" name="vendor_status"
                                                                        id="vendor-update-status">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-4">
                                                        <form
                                                            action="{{ route('admin.astrologers.manage.store-additional-detail') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="pandit_id"
                                                                value="{{ $overview['id'] }}">
                                                            <input type="hidden" name="type" value="vendor">

                                                            <div class="">
                                                                <h5>{{ !empty($overview->vendor_id) ? 'Update Vendor' : 'Add Vendor' }}
                                                                </h5>
                                                                @php
                                                                    $selectedVendorId = !empty($overview->vendor_id)
                                                                        ? json_decode($overview->vendor_id, true)['id']
                                                                        : null;
                                                                @endphp
                                                                <select name="vendor_id" id="" class="form-control"
                                                                    required>
                                                                    @foreach ($vendors as $vendor)
                                                                        <option value="{{ $vendor->id }}"
                                                                            {{ $selectedVendorId == $vendor->id ? 'selected' : '' }}>
                                                                            {{ $vendor->f_name . ' ' . $vendor->l_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="text-center py-3">
                                                                <button type="submit"
                                                                    class="btn btn-primary">{{ !empty($overview->vendor_id) ? 'Update' : 'Save' }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card w-100">
                                            <div class="card-header">
                                                <h5 class="mb-0">Event Organizer Detail</h5>
                                            </div>
                                            <div class="col-md-12 py-4">
                                                <div class="row">
                                                    @if (!empty($overview->event_id))
                                                        <div class="col-md-8">
                                                            <div class="d-flex align-items-baseline gap-5">
                                                                @php
                                                                    $eventId = json_decode($overview->event_id, true);
                                                                    $eventDetail = App\Models\EventOrganizer::where(
                                                                        'id',
                                                                        $eventId['id'],
                                                                    )->first();
                                                                @endphp

                                                                <img src="{{ url('/storage/app/public/event/organizer/' . $eventDetail->image) }}"
                                                                    alt="" width="100px">
                                                                <p>{{ $eventDetail->organizer_name }}</p>
                                                                <label class="switcher">
                                                                    <input type="checkbox" class="switcher_input"
                                                                        id="event-status"
                                                                        {{ $eventId['status'] == 1 ? 'checked' : '' }}>
                                                                    <span class="switcher_control"></span>
                                                                </label>
                                                                <form
                                                                    action="{{ route('admin.astrologers.manage.store-additional-detail') }}"
                                                                    method="post" id="event-status-form">
                                                                    @csrf
                                                                    <input type="hidden" name="pandit_id"
                                                                        value="{{ $overview['id'] }}">
                                                                    <input type="hidden" name="type" value="event">
                                                                    <input type="hidden" name="event_id"
                                                                        value="{{ $eventId['id'] }}">
                                                                    <input type="hidden" name="event_status"
                                                                        id="event-update-status">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-4">
                                                        <form
                                                            action="{{ route('admin.astrologers.manage.store-additional-detail') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="pandit_id"
                                                                value="{{ $overview['id'] }}">
                                                            <input type="hidden" name="type" value="event">

                                                            <div class="">
                                                                <h5>{{ !empty($overview->event_id) ? 'Update Event Organizer' : 'Add Event Organizer' }}
                                                                </h5>
                                                                @php
                                                                    $selectedEventId = !empty($overview->event_id)
                                                                        ? json_decode($overview->event_id, true)['id']
                                                                        : null;
                                                                @endphp
                                                                <select name="event_id" id=""
                                                                    class="form-control" required>
                                                                    @foreach ($events as $event)
                                                                        <option value="{{ $event->id }}"
                                                                            {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                                                                            {{ $event->organizer_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="text-center py-3">
                                                                <button type="submit"
                                                                    class="btn btn-primary">{{ !empty($overview->event_id) ? 'Update' : 'Save' }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card w-100">
                                            <div class="card-header">
                                                <h5 class="mb-0">Individual Commission</h5>
                                            </div>
                                            <div class="col-md-12 py-4">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <form
                                                            action="{{ route('admin.astrologers.manage.store-additional-detail') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="pandit_id"
                                                                value="{{ $overview['id'] }}">
                                                            <input type="hidden" name="type" value="commission">

                                                            <div class="mx-2">
                                                                <h5> Set Individual Commission</h5>
                                                                <div class="d-flex gap-3">
                                                                    <input type="number" name="individual_commission" id="" class="form-control" value="{{$overview->individual_commission}}" required>
                                                                    <div class="">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </div>
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
        </div>
    </div>
@endsection

@push('script')
    {{-- vendor status update --}}
    <script>
        $(document).on('change', '#vendor-status', function() {
            let status = $(this).is(':checked') ? 1 : 0;
            Swal.fire({
                title: 'Update Status',
                text: 'Are You Sure',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                icon: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#vendor-update-status').val(status);
                    $('#vendor-status-form').submit();
                }
            });
        });
    </script>

    {{-- event status update --}}
    <script>
        $(document).on('change', '#event-status', function() {
            let status = $(this).is(':checked') ? 1 : 0;
            Swal.fire({
                title: 'Update Status',
                text: 'Are You Sure',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                icon: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#event-update-status').val(status);
                    $('#event-status-form').submit();
                }
            });
        });
    </script>
@endpush
