@extends('layouts.back-end.app-event')

@section('title', translate('Employee_list'))

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Employee_list') }}
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
                            <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('Employee_list') }}
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
                                    <th>{{ translate('Employee_Info') }}</th>
                                     @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'Status'))
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
                                        <img style="width: 66px; height: 65px;" alt="" src="{{ getValidImage(path: 'storage/app/public/event/employee/' . $items['image'], type: 'backend-panchang') }}">
                                    </td>
                                    <td>
                                        <span>ID: {{ ucwords($items['identify_number']) }}</span><br>
                                        <span>Name: {{ ucwords($items['name']) }}</span><br>
                                        <span>Phone: {{ ucwords($items['phone']) }}</span><br>
                                        <span>Email: {{ ucwords($items['email']) }}</span><br>

                                    </td>
                                    @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'Status'))
                                    <td>
                                        <form action="{{route('event-vendor.employee.employee-status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$items['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="items-status{{ $items['id'] }}" value="1" {{ $items['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="items-status{{ $items['id'] }}" data-on-image="items-status-on.png" data-off-image="items-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' Employee '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' Employee '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_Employee_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_Employee_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif
                                    <td>

                                        <div class="d-flex justify-content-center gap-2">
                                            @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'Edit'))
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('event-vendor.employee.employee-edit',[$items['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif 
                                            @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'Delete'))
                                            <a class="event_artist-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
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
<span id="route-admin-event_artist-delete" data-url="{{ route('event-vendor.employee.employee_delete') }}"></span>

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
    $('.event_artist-delete-button').on('click', function() {
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
                    success: function(response) {
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