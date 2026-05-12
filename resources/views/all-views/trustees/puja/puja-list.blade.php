@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', 'Puja List')

@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endpush
@section('content')
@php
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">Puja List
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <span>Add Puja</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('trustees-vendor.puja-management.puja-save') }}" method="post">
                        @csrf
                        <div class='row'>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="puja_name" autocomplete="off" value="{{ old('puja_name') }}" class="form-control" placeholder="Enter Puja Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="rprice" class="form-control" autocomplete="off" value="{{ old('rprice') }}" placeholder="Enter Retailer Price" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="pprice" class="form-control" autocomplete="off" value="{{ old('pprice') }}" placeholder="Enter Purchase Price" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">

                <!-- Table displaying trust  -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="pujaOrdersTable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>SL</th>
                                    <th>Puja Name</th>
                                    <th>Retailer Price</th>
                                    <th>Purchase Price</th>
                                    <th>Discount</th>
                                    <th>Date</th>
                                    @if (Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'Edit') || Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'Delete'))
                                    <th>{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($pujaList)
                                <?php $p = 1; ?>
                                @foreach($pujaList as $val)
                                <tr>
                                    <td>{{ $p }}</td>
                                    <td>{{ $val['puja_name'] }}</td>
                                    <td>{{ $val['rprice'] }}</td>
                                    <td>{{ $val['pprice'] }}</td>
                                    <td>{{ $val['discount'] }}</td>
                                    <td>{{ date('d M Y h:i A',strtotime($val['created_at'])) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'Edit'))
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('trustees-vendor.puja-management.puja-edit',[$val['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if(Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'Delete'))
                                            <a class="trust-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $val['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <?php $p++; ?>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-trust-delete" data-url="{{ route('trustees-vendor.puja-management.puja-delete') }}"></span>
<!-- Toast message for trust deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    "use strict";
    $('#pujaOrdersTable').DataTable();
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
                        if (response.status == 1) {
                            toastr.success(response.message, '', {
                                positionClass: 'toast-bottom-left'
                            });
                        } else {
                            toastr.error(response.message, '', {
                                positionClass: 'toast-bottom-left'
                            });
                        }
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