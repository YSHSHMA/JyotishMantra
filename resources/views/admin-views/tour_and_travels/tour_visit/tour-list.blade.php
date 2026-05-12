@extends('layouts.back-end.app')
@php 
use App\Utils\Helpers;
@endphp
@section('title', translate('Tour_list'))

@section('content')
<style>
    .btn-tour-visit-empty {
        animation: pulse-danger 1s infinite;
        border-color: red;
        color: red;
    }

    @keyframes pulse-danger {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
        }
    }
</style>

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('Tour_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header d-block">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control search_by_name" placeholder="Search By Tour Name">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control search_by_type">
                                <option value="">Select Tour Variant</option>
                                <option value="0">Cities Tour</option>
                                <option value="1">Special Tour(With Date)</option>
                                <option value="4">Special Tour(Without Date)</option>
                                <option value="2">Daily Tour(With Address)</option>
                                <option value="3">Daily Tour(WithOut Address)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control search_by_cab_name">
                                <option value="">Select Create By</option>
                                <option value="admin">admin</option>
                                <option value="vendor">vendor</option>
                            </select>
                        </div>

                        @if (Helpers::modules_permission_check('Tour', 'Tour Manage', 'add-tour-visit'))                        
                        <div class="col-md-3">
                            <a href="{{route('admin.tour_visits.add-tour')}}" class="btn btn--primary">
                                <i class="tio-add"></i>
                                <span class="text">{{ translate('Add_Tour_visit') }}</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-start p-4">
                    <div class="table-responsive">
                        <table id="policy_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>#{{ translate('ID') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('tour_variant') }}</th>
                                    <th>{{ translate('tour_name') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('create_by') }}</th>
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

<div class="modal fade modal-center modal_order_view" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="tio-clear" aria-hidden="true"></i></button>
                <h4 class="modal-title">Booking cancel</h4>
                <div class="form-group view_orders_items">

                </div>

            </div>
        </div>
    </div>
</div>

{{-- whatsapp Model --}}
<div class="modal fade" id="whatsapp" tabindex="-1" role="dialog" aria-labelledby="whatsappTitleId"
    aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">WhatsApp</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="sendtest" method="post" class="modal-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="service-id">
                    <div class="form-group mb-2">
                        <label for="reciver">Mobile Number</label>
                        <input type="number" class="form-control" name="reciver" id="reciver" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-block">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

<script>
    $(document).on('click', '.open-whatsapp-modal', function() {
        const id = $(this).data('id');
        const slug = $(this).data('slug');
        const link = "{{ url('tour/tour-visit') }}/" + slug;

        $('#service-id').val(id);
        $('#reciver').val('');
        $('#message').val(`\n\n${link}`);
        $('#sendtest button.btn.btn-primary.btn-block').prop("disabled", false);
        $('#sendtest button.btn.btn-primary.btn-block').text("Send");
        $('#whatsapp').modal('show');
    });
</script>

<script>
    $('#sendtest').on('submit', function(e) {
        e.preventDefault();
        $('#sendtest button.btn.btn-primary.btn-block').prop("disabled", true);
        $('#sendtest button.btn.btn-primary.btn-block').text("Please Wait...");
        var formD = $(this).serialize();
        $.ajax({
            url: "{{ url('/admin/whatsapp/send-test-message') }}",
            method: "POST",
            data: formD,
            success: function(res) {
                $('#sendtest')[0].reset();

                $('#whatsapp').modal('hide');

                Swal.fire({
                    position: "top-end",
                    title: 'Message sent Successfully',
                    showConfirmButton: false,
                    timer: 1500,
                    buttonsStyling: false
                });
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>

<script>
    function booking_cancel(id) {
        $.ajax({
            url: "{{ route('admin.tour_visits.company-booking-order-get')}}",
            data: {
                id,
                _token: '{{ csrf_token() }}'
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                if (data.status == 1) {
                    $(".modal_order_view").modal('show');
                    var html = '';

                    html += `
                    <form action="{{ route('admin.tour_visits.company-booking-settlement')}}" method="post">
                    <div class="row">
                    @csrf
                        <div class="col-md-6 mt-3">
                        <input type="hidden" name="tour_id" value="${id}">
                            <label for="">Select type</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="type" id="" class="form-control" onchange="
    if (this.value == '2') {
        $('.transfor_cab_data').removeClass('d-none');
    } else {
        $('.transfor_cab_data').addClass('d-none');
    }">
                                <option value="1">All Refund</option>
                                <option value="2">Cab Transfer</option>
                                <option value="3">Cab Refund</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3 d-none transfor_cab_data">
                            <label for="">company name</label>
                        </div>
                        <div class="col-md-6 mt-3 d-none transfor_cab_data">
                            <select name="transfor_cab" class="form-control">`;
                    if (data.data.company_all) {
                        $.each(data.data.company_all, function(index, value) {
                            html += `<option value="${value['id']}">${value['company_name']}</option>`;
                        })
                    }
                    html += ` </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">company name</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="cab_id" class="form-control">`;
                    if (data.data.company) {
                        $.each(data.data.company, function(index, value) {
                            html += `<option value="${value['cab_assign']}">${(value?.company?.company_name ?? '')} || ${(value?.amount ?? '')} || ${(value?.qty ?? '')}</option>`;
                        })
                    }
                    html += ` </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="">user name</label>
                        </div>
                        <div class="col-md-6 mt-3">
                            <select name="order_id[]" multiple class="form-control">
                            <option value=""></option>`;
                    if (data.data.order_list) {
                        $.each(data.data.order_list, function(index, value) {
                            html += `<option value="${value['id']}">${(value?.user_data?.name ?? '')} || ${(value?.company?.company_name ?? '')} || ${(value?.amount ?? '')} || ${(value?.qty ?? '')}</option>`;
                        })
                    }
                    html += `</select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <input type="submit" class="btn btn--primary">
                        </div>
                        </div>
                        </form>
                    `;

                    $(".view_orders_items").html(html);
                    $('select[name="order_id[]"]').select2();
                } else {
                    toastr.error('Order booking is not available');
                }
                console.log(data);
            }
        })

    }
</script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#policy_table',
            ajaxUrl: "{{ route('admin.tour_visits.tour-list-filter') }}",
            exportTitle: "policy list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'tour_id',
                    name: 'tour_id'
                },
                {
                    data: 'tour_type',
                    name: 'tour_type'
                },
                {
                    data: 'use_date',
                    name: 'use_date'
                },
                {
                    data: 'tour_name',
                    name: 'tour_name'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'create_by',
                    name: 'create_by',
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
                        d.search_by_cabid = $('.search_by_cab_name').val();
                    }
                }
            }
        });
    });

    $('.search_by_type, .search_by_cab_name').on('change', function() {
        $('#policy_table').DataTable().draw();
    });
    let searchDelay;
    $('.search_by_name').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#policy_table').DataTable().draw();
        }, 500);
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
</script>
@endpush