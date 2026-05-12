@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('vehicle_list'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('vehicle_list') }}
        </h2>
    </div>
    <div class="card">
        <div class="card-header d-block">
            @if (Helpers::modules_permission_check('Tour', 'Tour Vehicle Type', 'add'))
            <a href="{{ route('admin.tour_vehicle_setting.add') }}" class="btn btn-primary float-end"> {{ translate('add') }}</a>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="vehicle_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>SL.</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
{{-- ck editor --}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
    $(document).ready(function() {
        initDataTable({
            tableId: '#vehicle_table',
            ajaxUrl: "{{ route('admin.tour_vehicle_setting.vehicle-list-filter') }}",
            exportTitle: "Trust Puja Orders",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                }, // serial no
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'name',
                    name: 'name'
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
            }
        });
    });

    $(document).on('click', '.delete-data', function(e) {
        e.preventDefault();
        let formId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            console.log(result.value);
            if (result.value) {
                $('#' + formId).submit();
            }
        });
    });

    $(document).on('change', '.toggle-switch-message', function(e) {
        e.preventDefault();

        const checkbox = $(this);
        const isChecked = checkbox.prop('checked');
        const formId = checkbox.closest('form').attr('id');
        const toggleId = checkbox.data('toggle-id');
        const onTitle = checkbox.data('on-title');
        const offTitle = checkbox.data('off-title');
        const onMessage = checkbox.data('on-message');
        const offMessage = checkbox.data('off-message');

        // Revert checkbox before confirmation
        checkbox.prop('checked', !isChecked);

        Swal.fire({
            title: isChecked ? onTitle : offTitle,
            html: isChecked ? onMessage : offMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.value) {
                checkbox.prop('checked', isChecked);
                $('#' + formId).submit(); // submit form
            }
        });
    });
</script>
@endpush