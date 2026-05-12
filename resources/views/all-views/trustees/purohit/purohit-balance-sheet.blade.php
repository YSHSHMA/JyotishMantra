@extends('layouts.back-end.app-trustees')
@section('title', translate('balance_Sheet'))
@php
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$Authtoken = auth('trust')->user()->auth_token;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$Authtoken = auth(trust_employee)->user()->auth_token;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
$Authtoken = auth('purohit')->user()->auth_token;
}
@endphp
@push('css_or_js')
<style>
    .date-option-btn {
        padding: 10px 18px;
        background-color: #f8f9fa;
        border: 1px solid #e1e5eb;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .date-option-btn:hover {
        background-color: #e9ecef;
    }

    .date-option-btn.active {
        background-color: #3498db;
        color: white;
        border-color: #3498db;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('balance_Sheet') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
        <div class="float-end">
            <button class="btn btn-success" onclick="withdrowal_models(`{{ request('id') }}`)">Pay Amount</button>
        </div>
    </div>

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-12">
                            <div class="date-options">
                                <button class="date-option-btn my-1" data-range="today">Today</button>
                                <button class="date-option-btn my-1" data-range="week">This Week</button>
                                <button class="date-option-btn my-1" data-range="lastWeek">Last Week</button>
                                <button class="date-option-btn my-1" data-range="month">This Month</button>
                                <button class="date-option-btn my-1" data-range="lastMonth">Last Month</button>
                                <button class="date-option-btn my-1" data-range="custom">Custom Range</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-custom input-group-merge d-none">
                                <input type="datetime-local" class="form-control start_date">
                                <input type="datetime-local" class="form-control end_date">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table id="accounthistory" class="table table-striped table-bordered table-hover">
                                        <thead class="thead-light text-capitalize">
                                            <tr>
                                                <th class="text-success font-weight-bold">Credit(+)</th>
                                                <th class="text-danger font-weight-bold">Debit(-)</th>
                                                <th class="text-primary font-weight-bold">Balance</th>
                                                <th>Date</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="purohit-modal-show" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign Ticket</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" class="form-control purohit_ids">
                    <input type="hidden" class="form-control purohitstatus" value="0">
                    <input type="hidden" class="form-control purohitEmpNames">
                    <input type="text" autocomplete='off' class="form-control purohit_employee_name_show" placeholder="Search employee...">
                    <ul class="list-group emp_suggestion_lists" style="display:none; position:absolute; z-index:1000; width:70%;"> </ul>
                </div>
                <div class="">
                    <input type="text" autocomplete='off' class="form-control emp_code" placeholder="Enter Employee Pay Code">
                    <span class="employee-warning-entered-invalid-code font-weight-bolder text-danger"></span>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-warning text-white" onclick="paymentCollect()">Cash Pay Update</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#accounthistory',
            ajaxUrl: "{{ route('trustees-vendor.purohit-data.purohit-balance-sheet-filters',['id'=>request('id')]) }}",
            exportTitle: "Trust Ledger Statement",
            pageLength: 10,
            notshowfooter: 1,
            columns: [{
                    data: 'credit',
                    name: 'credit',
                    title: "Credit (+)",
                    className: 'text-success font-weight-bold',
                    orderable: false
                },
                {
                    data: 'debit',
                    name: 'debit',
                    title: "Debit (-)",
                    className: 'text-danger font-weight-bold',
                    orderable: false
                },
                {
                    data: 'balance',
                    name: 'balance',
                    title: "Balance",
                    className: 'text-primary font-weight-bold',
                    orderable: false
                },
                {
                    data: 'date',
                    name: 'date',
                    title: "Date",
                    orderable: false
                },
                {
                    data: 'notes',
                    name: 'datnotese',
                    title: "Note",
                    orderable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                createdRow: function(row, data, dataIndex) {
                    if (parseFloat(data.status) == 0) {
                        $(row).addClass('bg-warning');
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();
                    if (json && json.footerData) {
                        $(api.column(2).footer()).html(
                            '<strong>Total: ' + json.footerData.totalAmount + '</strong>'
                        );
                    }
                },
                ajax: {
                    data: function(d) {
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.payment_mode = $('.payment_mode').val();
                        d.purohit_id = $('.purohit_id').val();
                    }
                },
            }
        });
    });


    $('.payment_mode, .start_date, .end_date,.purohit_id').on('change', function() {
        $('#accounthistory').DataTable().draw();
    });

    function setupDateRangePicker() {
        const dateOptionButtons = document.querySelectorAll('.date-option-btn');
        const startDateInput = document.querySelector('.start_date');
        const endDateInput = document.querySelector('.end_date');
        dateOptionButtons.forEach(button => {
            button.addEventListener('click', function() {
                $('.input-group-merge').addClass('d-none');
                dateOptionButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const range = this.getAttribute('data-range');
                const dates = calculateDateRange(range);
                const formattedStartDate = formatDateTimeLocal(dates.startDate);
                const formattedEndDate = formatDateTimeLocal(dates.endDate);
                startDateInput.value = formattedStartDate;
                endDateInput.value = formattedEndDate;
                setTimeout(() => {
                    $('#accounthistory').DataTable().draw();
                }, 50);
            });
        });

        // Function to calculate date ranges
        function calculateDateRange(range) {
            const now = new Date();
            let startDate, endDate;
            switch (range) {
                case 'today':
                    startDate = new Date(now);
                    endDate = new Date(now);
                    break;
                case 'week':
                    startDate = new Date(now);
                    startDate.setDate(now.getDate() - now.getDay() + (now.getDay() === 0 ? -6 : 1));
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                    break;

                case 'lastWeek':
                    startDate = new Date(now);
                    startDate.setDate(now.getDate() - now.getDay() + (now.getDay() === 0 ? -13 : -6));
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                    break;

                case 'month':
                    startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    startDate.setHours(0, 0, 0, 0);

                    endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                    endDate.setHours(23, 59, 59, 999);
                    break;

                case 'lastMonth':
                    startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(now.getFullYear(), now.getMonth(), 0);
                    endDate.setHours(23, 59, 59, 999);
                    break;

                case 'custom':
                    endDate = new Date();
                    endDate.setHours(23, 59, 59, 999);
                    startDate = new Date(endDate);
                    startDate.setDate(endDate.getDate() - 7);
                    startDate.setHours(0, 0, 0, 0);
                    $('.input-group-merge').removeClass('d-none');
                    break;

                default:
                    startDate = new Date(now);
                    startDate.setDate(now.getDate() - now.getDay() + (now.getDay() === 0 ? -6 : 1));
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
            }
            return {
                startDate,
                endDate
            };
        }

        // Function to format date for datetime-local input
        function formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Initialize with this week selected
        const thisWeekBtn = document.querySelector('[data-range="week"]');
        if (thisWeekBtn) {
            thisWeekBtn.classList.add('active');
            thisWeekBtn.click();
        }

        startDateInput.addEventListener('change', function() {
            dateOptionButtons.forEach(btn => {
                if (btn.getAttribute('data-range') === 'custom') {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });

        endDateInput.addEventListener('change', function() {
            dateOptionButtons.forEach(btn => {
                if (btn.getAttribute('data-range') === 'custom') {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', setupDateRangePicker);
    $(document).ready(function() {
        setupDateRangePicker();
    });

    function withdrowal_models(id) {
        $('.purohit_ids').val(id);
        $('#purohit-modal-show').modal('show');
        $('.purohitstatus').val(0);
        $('.purohitEmpNames').val('');
        $('.purohit_employee_name_show').val('');
    }
</script>
<script>
    $(document).ready(function() {
        let activeInput = null;
        $(document).on('keyup click', '.purohit_employee_name_show', function() {
            let keyword = $(this).val();
            activeInput = $(this);
            let suggestionBox = $(this).next('.emp_suggestion_lists');
            $.ajax({
                url: "{{url('api/v1/purohit-all-employee-list')}}",
                type: "GET",
                data: {
                    search: keyword,
                    purohit: $('.purohit_ids').val(),
                    amountadd: 1,
                },
                success: function(res) {
                    let list = '';
                    if (res.status && res.data.length > 0) {
                        $.each(res.data, function(i, item) {
                            list += `
                          <li class="list-group-item" data-name="${item.name}" data-id="${item.id}">
                              ${item.full_name}
                          </li>`;
                        });
                        suggestionBox.html(list).show();
                    } else {
                        suggestionBox.hide();
                    }
                }
            });
        });
        $(document).on('click', '.emp_suggestion_lists li', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let selectedName = $(this).text().trim();
            let selectedName2 = $(this).data('id');
            let suggestionBox = $(this).closest('.emp_suggestion_lists');
            let inputBox = suggestionBox.prev('.purohit_employee_name_show');
            inputBox.val(selectedName);
            $('.purohitEmpNames').val(selectedName2);
            $('.purohitstatus').val(1);
            suggestionBox.hide();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.purohit_employee_name_show, .emp_suggestion_lists').length) {
                $('.emp_suggestion_lists').hide();
            }
        });
    });

    function paymentCollect() {
        if ($('.purohitstatus').val() == 1) {
            if (($('.emp_code').val()).length < 3) {
                toastr.error('Please Select Employee Pay Code');
                return false;
            }
            $.ajax({
                url: "{{url('api/v3/seller/trust/trust-old-payment-success')}}",
                type: "post",
                headers: {
                    Authorization: "Bearer {{$Authtoken}}"
                },
                beforeSend: function() {
                    $('#loading').removeClass('d--none');
                },
                data: {
                    purohit_id: $('.purohit_ids').val(),
                    emp_id: $('.purohitEmpNames').val(),
                    emp_code: $('.emp_code').val(),
                },
                success: function(res) {
                    if (res.status == 1) {
                        $('#purohit-modal-show').modal('hide');
                        $('#accounthistory').DataTable().draw();
                    } else {
                        if (res.data > 1) {
                            $('.employee-warning-entered-invalid-code').text(`Please Enter Correct Code. You have already used this ${res.data} times`);
                        }
                    }
                    $('#loading').addClass('d--none');
                },
                error: function(xhr) {
                    $('#loading').addClass('d--none');
                }
            });
        } else {
            toastr.error('Please Select Employee');
        }
    }
</script>
@endpush