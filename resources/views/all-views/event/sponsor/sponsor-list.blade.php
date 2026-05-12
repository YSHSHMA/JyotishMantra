@extends('layouts.back-end.app-event')

@section('title', translate('Sponsor_List'))

@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush
@section('content')
@php
use App\Utils\Helpers;
@endphp

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Sponsor_List') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="card p-2">
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="sponsortable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('image') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('phone') }}</th>
                                    <th>{{ translate('package_list') }}</th>
                                    @if(Helpers::Employee_modules_permission('Sponsor Management', 'Sponsor List', 'Status'))
                                    <th>{{ translate('status') }}</th>
                                    @endif
                                    @if(Helpers::Employee_modules_permission('Sponsor Management', 'Sponsor List', 'Edit') || Helpers::Employee_modules_permission('Sponsor Management', 'Sponsor List', 'Delete'))
                                    <th>{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<span id="image-path-of-product-upload-icon"
    data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two"
    data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
<span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="message-want-to-add-or-update-this-product"
    data-text="{{ translate('want_to_add_this_product') }}"></span>
<span id="message-please-only-input-png-or-jpg"
    data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
<span id="message-product-added-successfully" data-text="{{ translate('service_added_successfully') }}"></span>
<span id="system-currency-code" data-value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>
<span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
<span id="get-root-path-for-toggle-modal-image" data-path="{{dynamicAsset(path: 'public/assets/back-end/img/modal')}}"></span>

<div class="modal fade" id="toggle-status-modal-tour-order" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0" data-dismiss="modal" aria-label="Close"><i class="tio-clear"></i></button>
            </div>
            <div class="modal-body px-4 px-sm-5 pt-0">
                <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                    <div class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                        <img src="" class="status-icon" alt="" width="30" />
                        <img src="" id="toggle-status-modal-tour-order-image" alt="" />
                    </div>
                    <h5 class="modal-title" id="toggle-status-modal-tour-order-title"></h5>

                    <div class="text-center" id="toggle-status-modal-tour-order-message"></div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn--primary min-w-120" id="toggle-status-modal-tour-order-ok-button-tour-order" data-dismiss="modal">Ok</button>
                    <button type="button" class="btn btn-danger-light min-w-120" id="toggle-status-modal-tour-order-cancel-button-tour-order" data-dismiss="modal">Cancel</button>
                </div>
            </div>
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
    $(document).ready(function() {
        initDataTable({
            tableId: '#sponsortable',
            ajaxUrl: "{{ route('event-vendor.sponsor.sponsor-list-filter') }}",
            exportTitle: "Sponsor list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                }, // serial no
                {
                    data: 'image',
                    name: 'image',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'package',
                    name: 'package',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.search_by_name = $('.search_by_name').val();
                        d.search_by_type = $('.search_by_type').val();
                        d.search_by_category = $('.search_by_category').val();
                        d.search_by_cabid = $('.search_by_cab_name').val();
                    }
                }
            }
        });
    });

    $('.search_by_type, .search_by_category, .search_by_cab_name').on('change', function() {
        $('#sponsortable').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#sponsortable').DataTable().draw();
        }, 500);
    });

    function toggleswitchs(that) {
        let rootPath = $('#get-root-path-for-toggle-modal-image').data('path');
        const modalId = $(that).data('modal-id')
        const toggleId = $(that).data('toggle-id');
        const onImage = rootPath + '/' + $(that).data('on-image');
        const offImage = rootPath + '/' + $(that).data('off-image');
        const onTitle = $(that).data('on-title');
        const offTitle = $(that).data('off-title');
        const onMessage = $(that).data('on-message');
        const offMessage = $(that).data('off-message');
        toggleModal1(modalId, toggleId, onImage, offImage, onTitle, offTitle, onMessage, offMessage)
    }

    function toggleModal1(modalId, toggleId, onImage = null, offImage = null, onTitle, offTitle, onMessage, offMessage) {
        if ($('#' + toggleId).is(':checked')) {
            $('#' + modalId + '-title').empty().append(onTitle);
            $('#' + modalId + '-message').empty().append(onMessage);
            $('#' + modalId + '-image').attr('src', onImage);
            $('#' + modalId + '-ok-button-tour-order').attr('toggle-ok-button', toggleId);
        } else {
            $('#' + modalId + '-title').empty().append(offTitle);
            $('#' + modalId + '-message').empty().append(offMessage);
            $('#' + modalId + '-image').attr('src', offImage);
            $('#' + modalId + '-ok-button-tour-order').attr('toggle-ok-button', toggleId);
        }
        $('#' + modalId + '-cancel-button-tour-order').attr('toggle-ok-button', toggleId);
        $('#' + modalId).modal('show');
    }


    $('#toggle-status-modal-tour-order-cancel-button-tour-order').on('click', function() {
        const toggleId = $('#' + $(this).attr('toggle-ok-button'));
        if (toggleId.is(':checked')) {
            toggleId.prop('checked', false);
        } else {
            toggleId.prop('checked', true);
        }
    });
    $('#toggle-status-modal-tour-order-ok-button-tour-order').on('click', function() {
        const toggleId = $('#' + $(this).attr('toggle-ok-button'));
        // if (toggleId.is(':checked')) {
        //     toggleId.prop('checked', false);
        // } else {
        //     toggleId.prop('checked', true);
        // }
        let toggleOkButton = $(this).attr('toggle-ok-button') + '-form';
        submitStatusUpdateForm1(toggleOkButton, this);
    });

    function submitStatusUpdateForm1(formId, that) {
        const form = $('#' + formId);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(data) {
                if (data.success == 1) {
                    toastr.success(data.message);
                } else {
                    // const toggleId = $('#' + $(that).attr('toggle-ok-button'));
                    // if (toggleId.is(':checked')) {
                    //     toggleId.prop('checked', false);
                    // } else {
                    //     toggleId.prop('checked', true);
                    // }
                    toastr.error(data.message);
                }
                $('#sponsortable').DataTable().draw();
            }
        })
    }
</script>
@endpush