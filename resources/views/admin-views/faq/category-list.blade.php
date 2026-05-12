@extends('layouts.back-end.app')

@section('title', translate('faq_category'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('faq_category') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new faq_category -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.faq.category-store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <label class="title-color" for="name">{{ translate('category_name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="{{ translate('enter_category_name') }}" required>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section for displaying event categiry list -->
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <!-- Search bar -->
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                            <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('category_list') }}
                                <span class="badge badge-soft-dark radius-50 fz-12">{{ ($category_list->total()??'') }}</span>
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
                <!-- Table displaying event category -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through items -->
                                @foreach($category_list as $key => $items)
                                <tr>
                                    <td>{{$category_list->firstItem()+$key}}</td>
                                    <td>{{ $items['name'] }}</td>
                                    <td>
                                        <!-- Form for toggling status -->
                                        <form action="{{route('admin.faq.category-status-update') }}" method="post" id="items-status{{$items['id']}}-form">
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
                                                    data-on-title="{{ translate('Want_to_Turn_ON').' '.$items['defaultname'].' '. translate('status') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.$items['defaultname'].' '.translate('status') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_this_faq_category_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_faq_category_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{ translate('edit') }}"
                                                href="{{route('admin.faq.category-update',['id'=>$items['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="event_category-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
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
                <!-- Pagination for event category list -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {!! $category_list->links() !!}
                    </div>
                </div>
                <!-- Message for no data to show -->
                @if(count($category_list) == 0)
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
<span id="route-admin-event_category-delete" data-url="{{ route('admin.faq.category-delete') }}"></span>
<!-- Toast message for event category deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-body">
            {{ translate('event_category_deleted') }}
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
    $('.event_category-delete-button').on('click', function() {
        let categoryId = $(this).attr("id");
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
                    url: $('#route-admin-event_category-delete').data('url'),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: categoryId
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