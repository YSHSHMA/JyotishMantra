@extends('layouts.back-end.app')

@section('title', translate('self_vehicle_leads'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


    .bg-label-primary {
        background-color: #007bff;
        color: #fff;
    }

    .bg-label-primary:hover {
        background-color: #0056b3;
    }

    .bg-label-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .bg-label-danger:hover {
        background-color: #c82333;
    }

    .bg-label-success {
        background-color: #28a745;
        color: #fff;
    }

    .bg-label-success:hover {
        background-color: #218838;
    }

    .bg-label-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .bg-label-info:hover {
        background-color: #117a8b;
    }

    .bg-label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .bg-label-warning:hover {
        background-color: #e0a800;
    }

    .dropdown-menufollow {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 1rem;
        width: 225px;
        margin-right: 13rem;
        text-align: center;
        display: flex;
        gap: 0.5rem;
        position: absolute;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-center {
        justify-content: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .myactionbtn {
        width: 1.625rem !important;
        height: 1.625rem !important;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('self_vehicle_leads') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1 my-2">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <input type="datetime-local" class="form-control start_date">
                                <input type="datetime-local" class="form-control end_date">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group input-group-custom input-group-merge">
                                <select class="search_by_status form-control">
                                    <option value="">Select Payment status</option>
                                    <option value="0">pending</option>
                                    <option value="1">Complete</option>
                                    <option value="2">Failed</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="vipOrderTable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('platform') }}</th>
                                    <th>{{ translate('User_name') }}</th>
                                    <th>{{ translate('vehicle_info') }}</th>
                                    <th class="text-center">{{ translate('booking_date') }}</th>
                                    <th>{{ translate('via_wallet') }}</th>
                                    <th>{{ translate('via_online') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('Follow_By') }}</th>
                                    <th>{{ translate('Follow_Date') }}</th>
                                    <th>{{ translate('Next_Date') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
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

<div class="modal fade" id="followUpModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followUpModalTitleId">
                    Follow Up
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.self-driving-management.self-vehicle-follow-up') }}" method="POST">
                @csrf
                @php
                if (auth('admin')->check()) {
                $adminId = App\Models\Admin::where('id', auth('admin')->id())->first();
                }
                @endphp
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="follow_by_id" id="followUpFollowId" class="form-control"
                            value="{{ $adminId['id'] }}">
                        <input type="hidden" name="follow_by" id="followUpFollowId" class="form-control"
                            value="{{ $adminId['name'] }}">
                        <input type="hidden" name="customer_id" id="followUpUserId" class="form-control">
                        <input type="hidden" name="lead_id" id="followUpLeadID" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="" class="form-label">Date</label>
                            <input type="text" name="last_date" class="form-control" value="{{ now() }}" readonly="" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="" class="form-label">Message</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Enter Message"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="" class="form-label">Next Follow Up Date</label>
                            <input type="text" name="next_date" class="form-control" id="next_date" required="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Histore --}}
<div class="modal fade" id="followUpHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followUpModalHistoryTitleId">
                    Follow Up History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-dark">
                                <tr>
                                    <th scope="col" class="text-white">#</th>
                                    <th scope="col" class="text-white">Follow By</th>
                                    <th scope="col" class="text-white">Last Followup</th>
                                    <th scope="col" class="text-white">Message</th>
                                    <th scope="col" class="text-white">Next Followup</th>
                                </tr>
                            </thead>
                            <tbody id="followUpPoojaHistoryTBody">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
</script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script>
    // datepicker
    var today = new Date();
    var tomorrow = new Date(today);
    tomorrow.setDate(today.getDate());
    $('#next_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy/mm/dd',
        modal: true,
        footer: true,
        minDate: tomorrow,
        todayHighlight: true
    });
</script>
<script>
    $('#date_type').change(function(e) {
        e.preventDefault();

        var value = $(this).val();
        if (value == 'custom_date') {
            $('#from-to-div').show();
        } else {
            $('#from-to-div').hide();
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.btn[data-bs-toggle="dropdown"]').click(function() {
            var $dropdownMenu = $(this).siblings('.dropdown-menufollow');
            $('.dropdown-menufollow').not($dropdownMenu).hide(); // Hide all other dropdown menus
            $dropdownMenu.toggle();
        });
    });
</script>
<script>
    function followUp(that) {
        var id = $(that).attr('data-custId');
        var lead = $(that).attr('data-leadsId');
        console.log(lead);
        $('#followUpLeadID').val(lead);
        $('#followUpUserId').val(id);
        $('#followUpModal').modal('show');
    }
</script>
<script>
    function followHistory(that) {
        var leadId = $(that).attr('data-leadsId');
        var types = $(that).attr('data-type');
        var row = "";
        $.ajax({
            url: "{{ url('admin/self-driving-management/self-vehicle-get-follow-up') }}/" + leadId,
            method: 'GET',
            //   data:{id:leadId},
            success: function(response) {
                console.log(response);
                $('#followUpPoojaHistoryTBody').html('');
                if (response.length != 0) {
                    $.each(response, function(key, value) {
                        row += `<tr> <td>${key + 1}</td> <td>${value.follow_by}</td> <td>${new Date(value.last_date).toLocaleDateString('en-GB')}</td> <td>${value.message}</td> <td>${new Date(value.next_date).toLocaleDateString('en-GB')}</td> </tr>`;
                    });
                } else {
                    row = '<tr> <td colspan="5" class="text-center"> No Data Available </td> </tr>';
                }
                $('#followUpPoojaHistoryTBody').append(row);
            },
        });
        $('#followUpHistoryModal').modal('show');
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#vipOrderTable',
            ajaxUrl: "{{ route('admin.self-driving-management.self-driving-lead-filter') }}",
            exportTitle: "vehicle Lead Orders",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'platform',
                    name: 'platform',
                },
                {
                    data: 'use_info',
                    name: 'use_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tour_name',
                    name: 'tour_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'create_by',
                    name: 'create_by'
                },
                {
                    data: 'via_wallet',
                    name: 'via_wallet'
                },
                {
                    data: 'via_online',
                    name: 'via_online'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'follow_by',
                    name: 'follow_by',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'next_date',
                    name: 'next_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'last_date',
                    name: 'last_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                }
            ],
            rowCallback: function(row, data, index) {
                console.log(data);
                if (data.status === 'Success') {
                    $(row).css('background-color','#addc9eb0');
                } else if (data.status === 'Pending') {
                    $(row).css('background-color','#f0f05b7a');
                } else {
                    $(row).css('background-color','#d903034a');
                }
            },
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.searchValue = $('#datatableSearch_').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.search_by_status = $('.search_by_status ').val();
                    }
                }
            }
        });
    });

    $('.start_date, .end_date, .search_by_status').on('change', function() {
        $('#vipOrderTable').DataTable().draw();
    });
    let searchDelay;
    $('#datatableSearch_').on('keyup', function() {
        clearTimeout(searchDelay);
        searchDelay = setTimeout(function() {
            $('#vipOrderTable').DataTable().draw();
        }, 500);
    });
</script>
@endpush