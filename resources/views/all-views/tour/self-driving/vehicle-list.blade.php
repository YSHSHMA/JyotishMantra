@extends('layouts.back-end.app-tour')
@section('title', translate('self_driving_list'))
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('self_driving_list') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-block">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control search_by_name">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control search_by_type type_id" onchange="getCategories()">
                                <option value="">Select Type</option>
                                @if($typeList)
                                @foreach($typeList as $val)
                                <option value="{{ $val['id']}}">{{$val['type']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control search_by_category type_vehicle_cateogry_id" onchange="getVehicles()">
                                <option value="">Select Category</option>
                                @if($brand_name)
                                @foreach($brand_name as $val1)
                                <option value="{{ $val1['id']}}">{{$val1['brand_name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control search_by_cab_name vehicle_list">
                                <option value="">Select Cab</option>
                                @if($cab_list)
                                @foreach($cab_list as $val2)
                                <option value="{{ $val2['id']}}">{{$val2['name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-start p-4">
                    <div class="table-responsive">
                        <table id="policy_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('category') }}</th>
                                    <th>{{ translate('cab_name') }}</th>
                                    <th>{{ translate('basic_price') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('approvel_status') }}</th>
                                    <th>{{ translate('action') }}</th>
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

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    function getCategories() {
        $.ajax({
            url: "{{ route('tour-vendor.self-driving.vehicle_category') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('.type_id').val()
            },
            success: function(response) {
                if (response.success == 1) {
                    let html = `<option value="" selected >{{ translate('select_vehicle_category') }}</option>`;
                    response.data.forEach(function(item) {
                        html += `<option value="${item.id}">${item.brand_name}</option>`;
                    });
                    $('.type_vehicle_cateogry_id').html(html);
                } else {
                    toastr.error(response.message, '', {
                        positionClass: 'toast-bottom-left'
                    });
                }
            }
        })
    }

    function getVehicles() {
        $.ajax({
            url: "{{ route('tour-vendor.self-driving.get-cab-list') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('.type_vehicle_cateogry_id').val()
            },
            success: function(response) {
                if (response.success == 1) {
                    let html = `<option value="" selected>{{ translate('select_cab') }}</option>`;
                    response.data.forEach(function(item) {
                        html += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('.vehicle_list').html(html);
                } else {
                    toastr.error(response.message, '', {
                        positionClass: 'toast-bottom-left'
                    });
                }
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#policy_table',
            ajaxUrl: "{{ route('tour-vendor.self-driving.self-drivinglist-filter') }}",
            exportTitle: "Self Driving list",
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
                    data: 'category',
                    name: 'category',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'cab_name',
                    name: 'cab_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'basic_price',
                    name: 'basic_price'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'is_approve',
                    name: 'is_approve',
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
        $('#policy_table').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#policy_table').DataTable().draw();
        }, 500);
    });

    $(document).ready(function() {
        $(document).on('change', '.toggle-switch-message', function(e) {
            e.preventDefault();

            const $checkbox = $(this);
            const form = $checkbox.closest('form');
            const isChecked = $checkbox.is(':checked');

            // Extract data
            const title = isChecked ? $checkbox.data('on-title') : $checkbox.data('off-title');
            const html = isChecked ? $checkbox.data('on-message') : $checkbox.data('off-message');

            $checkbox.prop('checked', !isChecked);

            Swal.fire({
                title: title,
                html: html,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    $checkbox.prop('checked', isChecked);
                    form.submit();
                }
            });
        });
    });

    $(document).on('click', '.delete-data', function(e) {
        e.preventDefault();

        let formId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.value) {
                $('#' + formId).submit();
            }
        });
    });
</script>
@endpush