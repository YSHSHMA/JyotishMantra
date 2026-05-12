@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Trust_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
                {{ translate('Trust_List') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12 mb-2">
                <div class='row'>
                    @if ($types == 1)
                        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                            <a class="order-stats order-stats_confirmed"
                                href="{{ route('admin.donate_management.trust.list') }}">
                                <div class="order-stats__content">
                                    <i class="tio-all_done">all_done</i>
                                    <h6 class="order-stats__subtitle">{{ translate('All_Trust') }}</h6>
                                </div>
                                <span class="order-stats__title">
                                    @php
                                        echo \App\Models\DonateTrust::count();
                                    @endphp
                                </span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                            <a class="order-stats order-stats_confirmed"
                                href="{{ route('admin.donate_management.trust.list', ['is_approve' => '1']) }}">
                                <div class="order-stats__content">
                                    <i class="tio-all_done">all_done</i>
                                    <h6 class="order-stats__subtitle">{{ translate('Approve') }}</h6>
                                </div>
                                <span class="order-stats__title">

                                    @php
                                        echo \App\Models\DonateTrust::where('is_approve', '1')->count();
                                    @endphp

                                </span>

                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                            <a class="order-stats order-stats_confirmed"
                                href="{{ route('admin.donate_management.trust.list', ['is_approve' => '0']) }}">
                                <div class="order-stats__content">
                                    <i class="tio-all_done">all_done</i>
                                    <h6 class="order-stats__subtitle">{{ translate('Not Approve') }}</h6>
                                </div>
                                <span class="order-stats__title">

                                    @php
                                        echo \App\Models\DonateTrust::where('is_approve', '0')->count();
                                    @endphp

                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <!-- Search bar -->
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
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
                    <!-- Table displaying trust  -->
                    <div class="text-start">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Trust_ID') }}</th>
                                        <th>{{ translate('Category_name') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('Trust_Name') }}</th>
                                        @if (Helpers::modules_permission_check('Donate', 'Trust All', 'status') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'status') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'status') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'status'))
                                            <th>{{ translate('status') }}</th>
                                        @endif

                                        <th>{{ translate('Verification_status') }}</th>
                                        @if (Helpers::modules_permission_check('Donate', 'Trust All', 'detail') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'detail') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'detail') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'detail') ||
                                                Helpers::modules_permission_check('Donate', 'Trust All', 'edit') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'edit') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'edit') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'edit') ||
                                                Helpers::modules_permission_check('Donate', 'Trust All', 'delete') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'delete') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'delete') ||
                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'delete'))
                                            <th>{{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loop through items -->
                                    @foreach ($all_trust as $key => $items)
                                        <tr>
                                            <td>{{ $all_trust->firstItem() + $key }}</td>
                                            <td><a href="{{ route('admin.donate_management.trust.trust-detail', [$items['id']]) }}"
                                                    class='font-weight-bold text-secondary'>{{ $items['trust_id'] }}
                                                    @if (\App\Models\DonateAllTransaction::where('amount_status', 0)->where('trust_id', $items['id'])->where('type', 'withdrawal')->count() > 0)
                                                        <!-- <span class="badge btn-soft-danger badge-pill ml-1"> -->
                                                        <i class="tio-saving_outlined">saving_outlined</i>
                                                        <!-- </span> -->
                                                    @endif
                                                </a>
                                                <br>
                                                <?php
                                                $getdata_show = \App\Models\Seller::where('relation_id', $items['id'])->first();
                                                ?>
                                                @if (!empty($getdata_show) && $getdata_show['reupload_doc_status'] == 2)
                                                    <span class="badge badge-soft-warning badge-pill ml-1">Pending
                                                        Doc</span>
                                                @elseif(!empty($getdata_show) && $getdata_show['reupload_doc_status'] == 3)
                                                    <span class="badge badge-soft-success badge-pill ml-1">New Doc
                                                        Updated</span>
                                                @endif
                                            </td>
                                            <td>{{ $items['category']['name'] ?? '' }}</td>
                                            <td>{{ $items['name'] ?? '' }}</td>
                                            <td>{{ $items['trust_name'] ?? '' }}</td>
                                            @if (Helpers::modules_permission_check('Donate', 'Trust All', 'status') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Pending', 'status') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Approved', 'status') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Canceled', 'status'))
                                                <td>
                                                    <form
                                                        action="{{ route('admin.donate_management.trust.status-update') }}"
                                                        method="post" id="items-status{{ $items['id'] }}-form">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $items['id'] }}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message" name="status"
                                                                id="items-status{{ $items['id'] }}" value="1"
                                                                {{ $items['status'] == 1 ? 'checked' : '' }}
                                                                data-modal-id="toggle-status-modal"
                                                                data-toggle-id="items-status{{ $items['id'] }}"
                                                                data-on-image="items-status-on.png"
                                                                data-off-image="items-status-off.png"
                                                                data-on-title="{{ translate('Want_to_Turn_ON') . ' Trust ' . translate('status') }}"
                                                                data-off-title="{{ translate('Want_to_Turn_OFF') . ' Trust ' . translate('status') }}"
                                                                data-on-message="<p>{{ translate('if_enabled_this_Trust_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                data-off-message="<p>{{ translate('if_disabled_this_Trust_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                            @endif
                                            <td>
                                                @if ($items['is_approve'] == 1)
                                                    Approve
                                                @elseif($items['is_approve'] == 2)
                                                    Send Request
                                                @elseif($items['is_approve'] == 3)
                                                    Reject
                                                @else
                                                    Pending
                                                @endif
                                            </td>
                                            @if (Helpers::modules_permission_check('Donate', 'Trust All', 'detail') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Pending', 'detail') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Approved', 'detail') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Canceled', 'detail') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust All', 'edit') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Pending', 'edit') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Approved', 'edit') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Canceled', 'edit') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust All', 'delete') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Pending', 'delete') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Approved', 'delete') ||
                                                    Helpers::modules_permission_check('Donate', 'Trust Canceled', 'delete'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::modules_permission_check('Donate', 'Trust All', 'detail') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'detail') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'detail') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'detail'))
                                                            <a class="btn btn-sm btn-outline-success"
                                                                href="{{ route('admin.donate_management.trust.trust-detail', [$items['id']]) }}"><i
                                                                    class="tio-invisible"></i></a>
                                                        @endif

                                                        @if (Helpers::modules_permission_check('Donate', 'Trust All', 'edit') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'edit') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'edit') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'edit'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="{{ translate('edit') }}"
                                                                href="{{ route('admin.donate_management.trust.update', [$items['id']]) }}">
                                                                <i class="tio-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Helpers::modules_permission_check('Donate', 'Trust All', 'delete') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Pending', 'delete') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Approved', 'delete') ||
                                                                Helpers::modules_permission_check('Donate', 'Trust Canceled', 'delete'))
                                                            <a class="trust-delete-button btn btn-outline-danger btn-sm square-btn"
                                                                id="{{ $items['id'] }}">
                                                                <i class="tio-delete"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination for trust list -->
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {!! $all_trust->links() !!}
                        </div>
                    </div>
                    <!-- Message for no data to show -->
                    @if (count($all_trust) == 0)
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
    <span id="route-admin-trust-delete" data-url="{{ route('admin.donate_management.trust.delete') }}"></span>
    <!-- Toast message for trust deleted -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="toast-body">
                {{ translate('Trust_deleted_Successfully') }}
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
        $('.trust-delete-button').on('click', function() {
            let TrustId = $(this).attr("id");
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
                    // Send AJAX request to delete trust caregory
                    $.ajax({
                        url: $('#route-admin-trust-delete').data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: TrustId
                        },
                        success: function(response) {
                            // Show success message
                            toastr.success('Trust deleted successfully', '', {
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
