@extends('layouts.back-end.app')

@section('title', translate('offlinepooja_leads_list'))

@php
    use App\Utils\Helpers;
@endphp
@push('css_or_js')
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
        }


        .card-col-5 {
            flex: 0 0 20%;
            /* 5 in a row */
            max-width: 20%;
        }

        @media (max-width: 992px) {

            /* tablet */
            .card-col-5 {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }

        @media (max-width: 768px) {

            /* mobile */
            .card-col-5 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {

            /* extra small mobile */
            .card-col-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .list-item-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
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
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('offlinepooja_leads_list') }}
            </h2>
        </div>

        <!-- Filter + Table -->
        <div class="row mt-4">
            <div class="col-md-12">

                <div class="card mb-4 shadow-sm rounded">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                        <!-- Left side (image + title) -->
                        <div class="d-flex align-items-center gap-2">
                            <img width="22" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                                alt="">
                            <h5 class="mb-0 font-weight-bold">{{ translate('Filter by any Information') }}</h5>
                        </div>

                        <!-- Right side (button) -->
                        @if (Helpers::modules_permission_check('Offline Pooja', 'Lead', 'add'))
                        <a title="Add Leads" class="btn btn-success btn-md"
                            href="{{ route('admin.leads.offline-addNewLeads') }}" target="_blank">
                            <i class="tio-add"></i> Add Leads
                        </a>
                        @endif
                    </div>


                    <div class="card-body py-3 px-4">
                        <div class="row">
                            {{-- @foreach ($data as $counts) --}}
                            <div class="card-col-12 w-100 mb-3">
                                <div class="card shadow-sm h-100 border-0 rounded-lg">
                                    <h6
                                        class="card-header text-capitalize font-weight-bold mb-3 d-flex justify-content-between">
                                        <span style="font-size: 13px">{{ 'offlinepooja' }}</span>
                                        <span>{{ count($data) }}</span>
                                    </h6>

                                    <div class="card-body row text-center p-3">
                                        <div class="col-md-4">
                                            <span class="badge badge-success px-4 py-2" style="font-size: 13px;">Complete:
                                                {{ $data['complete'] }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="badge badge-warning px-4 py-2" style="font-size: 13px;">Pending:
                                                {{ $data['pending'] }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="badge badge-danger px-4 py-2" style="font-size: 13px;">Failed:
                                                {{ $data['failed'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- @endforeach --}}
                        </div>

                        <div class="row">

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="datatableSearch_" class="font-weight-bold small text-muted">Search for
                                    Any</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i
                                                class="tio-search"></i></span>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue"
                                        class="form-control border-left-0" placeholder="{{ translate('search_by_name') }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="payment_status" class="font-weight-bold small text-muted">Payment Status</label>
                                <select class="service_id form-control">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="Complete">Complete</option>
                                    <option value="failed">failed</option>
                                </select>
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-2">
                                <input type="date" id="filterDate" class="form-control" placeholder="Select Date">
                            </div>
                            <div class="col-12 col-md-5 mb-2">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <button id="filterBtn" class="btn btn-primary btn-md w-100">Apply</button>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <button id="resetBtn" class="btn btn-secondary btn-md w-100">Reset</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap mb-2">

                            <!-- Left Side: Heading -->
                            <h2 class="h1 mt-2 d-flex gap-2 mb-0">
                                <img width="20"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                                    alt="">
                                {{ translate('Offlinepooja Leads Information List') }}
                            </h2>

                            <!-- Right Side: Color Legend -->
                            <div class="d-flex flex-wrap gap-3 mt-2 ms-auto">
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #007bff; display: inline-block; border-radius: 3px;"></span>
                                    <small>Web</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #28a745; display: inline-block; border-radius: 3px;"></span>
                                    <small>App</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #3b5998; display: inline-block; border-radius: 3px;"></span>
                                    <small>Facebook</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #ff9800; display: inline-block; border-radius: 3px;"></span>
                                    <small>Ads</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #c13584; display: inline-block; border-radius: 3px;"></span>
                                    <small>Instagram</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #6c757d; display: inline-block; border-radius: 3px;"></span>
                                    <small>Default</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #28a745; display: inline-block; border-radius: 3px;"></span>
                                    <small>Complete</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #ffc107; display: inline-block; border-radius: 3px;"></span>
                                    <small>Pending</small>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        style="width: 15px; height: 15px; background-color: #dc3545; display: inline-block; border-radius: 3px;"></span>
                                    <small>Failed</small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="px-3 pb-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <div style="overflow: auto;">
                                    <table id="table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th style="width: 200px;">User info</th>
                                                <th style="width: 200px;">Service Name & Order Id</th>
                                                <th>Order Date Time</th>
                                                <th>Booking date</th>
                                                <th>package price</th>
                                                <th>via wallet</th>
                                                <th>via online</th>
                                                <th>coupon</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th style="width: 200px;">User info</th>
                                                <th style="width: 200px;">Service Name & Order Id</th>
                                                <th>Order Date Time</th>
                                                <th>Booking date</th>
                                                <th>package price</th>
                                                <th>via wallet</th>
                                                <th>via online</th>
                                                <th>coupon</th>
                                                <th>Amount</th>
                                                <th>Action</th>
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

    {{-- Model --}}
    <div class="modal fade" id="followUpModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header flex-column align-items-start">
                    <h5 class="modal-title mb-1" id="followUpModalTitleId">
                        Follow Up <small class="text-muted">(Admin)</small>
                    </h5>
                    <p class="mb-0 text-secondary" style="font-size: 13px;">
                        Here you can manage and record the follow-up details for the selected lead.
                    </p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.leads.offline-lead-follow-up') }}" method="POST">
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
                            <input type="hidden" name="pooja_id" id="followUpPoojaId" class="form-control">
                            <input type="hidden" name="customer_id" id="followUpUserId" class="form-control">
                            <input type="hidden" name="lead_id" id="followUpLeadID" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Service Name</label>
                                <input type="text" name="service_name" id="followUpServiceName" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Date</label>
                                <input type="text" name="last_date" class="form-control" value="{{ now() }}"
                                    readonly="" required="">
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
                                <input type="text" name="next_date" class="form-control" id="next_date"
                                    required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect waves-light"
                            data-bs-dismiss="modal">
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
                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        const leadDeleteBaseUrl = "{{ route('admin.leads.offline-lead-delete', ':leadno') }}";
        const whatsappBaseUrl = "{{ route('admin.leads.offline-send-whatsapp-leads', ':leadno') }}";
        const whatsappUpdateBaseUrl = "{{ route('admin.whatsapp.offline-pooja-template') }}";
    </script>
    {{-- <script>
        $(document).ready(function() {
            initDataTable({
                tableId: '#table',
                ajaxUrl: '{{ route('admin.leads.offline-leads-list') }}',
                columns: [{
                        data: 'id',
                        title: 'ID'
                    },
                    {
                        data: null,
                        title: 'User Info',
                        render: function(data, type, row) {
                            return row.person_name + '<br><small>' + row.person_phone + '</small>';
                        }
                    },
                    {
                        data: 'platform',
                        title: 'Platform'
                    },
                    {
                        data: 'pooja_name',
                        title: 'Pooja Name',
                        defaultContent: '-'
                    },
                    {
                        data: 'order_created',
                        title: 'Order Created'
                    },
                    {
                        data: 'order_id',
                        title: 'Order ID'
                    },
                    {
                        data: 'package_name',
                        title: 'Package Name'
                    },
                    {
                        data: 'package_main_price',
                        title: 'Package Main Price'
                    },
                    {
                        data: 'package_price',
                        title: 'Package Price'
                    },
                    {
                        data: 'payment_status',
                        title: 'Payment Status'
                    },
                    {
                        data: 'payment_type',
                        title: 'Payment Type'
                    },
                    {
                        data: 'via_wallet',
                        title: 'Via Wallet'
                    },
                    {
                        data: 'via_online',
                        title: 'Via Online'
                    },
                    {
                        data: 'coupon_amount',
                        title: 'Coupon Amount'
                    },
                    {
                        data: 'final_amount',
                        title: 'Final Amount'
                    },
                    {
                        data: 'remain_amount',
                        title: 'Remain Amount'
                    },
                ],
                exportTitle: 'Leads List'
            });
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            let table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false, // Turn off responsive for proper fixed header + horizontal scroll
                searching: true,
                pageLength: 20,
                scrollY: '500px',
                scrollX: true, // enable horizontal scroll
                scrollCollapse: true,
                paging: true,
                fixedHeader: true, // fixed header
                fixedFooter: true, // fixed footer
                autoWidth: false, // prevent auto layout issues
                buttons: [{
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible' // Export all visible columns
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        },
                        orientation: 'landscape', // Optional: Wider format for more columns
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                dom: 'Blfrtip',
                ajax: {
                    url: "{{ route('admin.leads.offline-Showleads') }}",
                    type: "GET",
                    data: function(d) {
                        d.search_text = $('#searchInput').val();
                        d.date = $('#filterDate').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.service_id = $('.service_id').val();
                        // d.serviceType = $('.serviceType').val();
                        d.searchValue = $('#datatableSearch_').val();

                    }
                },

                columns: [{
                        data: 'id',
                        render: function(data, type, row) {
                            let rawStatus = (row.payment_status || '').toLowerCase();
                            let bgColor = '';
                            let label = '';

                            if (rawStatus === 'pending') {
                                label = 'Pending';
                                bgColor = '#ffc107'; // yellow
                            } else if (rawStatus === 'half') {
                                label = 'Half';
                                bgColor = '#ff9800'; // orange
                            } else if (rawStatus === 'complete') {
                                label = 'Complete';
                                bgColor = '#28a745'; // green
                            } else if (rawStatus === 'failed') {
                                label = 'Failed';
                                bgColor = '#dc3545'; // red
                            } else {
                                return `<div class="px-2 d-flex align-items-center">${data}</div>`; // No badge if status is unknown
                            }

                            return `
                                    <div style="display: flex; align-items: stretch; min-height: 80px;">
                                        <div style="
                                            width: 28px;
                                            background-color: ${bgColor};
                                            color: white;
                                            font-weight: bold;
                                            font-size: 11px;
                                            writing-mode: vertical-rl;
                                            text-orientation: mixed;
                                            text-transform: uppercase;
                                            display: flex;
                                            justify-content: center;
                                            align-items: center;
                                            border-radius: 0 4px 4px 0;
                                        ">
                                            ${label}
                                        </div>
                                        <div style="flex: 1; padding-left: 10px; display: flex; align-items: center;">
                                            ${data}
                                        </div>
                                    </div>
                                `;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            td.style.paddingLeft = '0px';
                        }
                    },

                    {
                        data: 'useinfo'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let orderId = row.order_id ?? '';
                            let platform = (row.platform ?? '').toLowerCase().trim();
                            // let serviceType = row.service_type ?? '';
                            let serviceName = row.service_name ?? '';
                            let serviceUrl = row.service_url ?? '#';

                            // Platform-specific colors
                            let platformColor = '#6c757d'; // default gray
                            if (platform === 'web') platformColor = '#007bff'; // blue
                            else if (platform === 'app') platformColor = '#17a2b8'; // cyan
                            else if (platform === 'instagram') platformColor = '#e1306c'; // pink
                            else if (platform === 'facebook') platformColor = '#1877f2'; // fb blue
                            else if (platform === 'ads') platformColor = '#ff9800'; // orange
                            else if (platform === 'admin') platformColor = '#28a745'; // green

                            return `
                                <div style="display: flex; align-items: stretch; min-height: 80px;">
                                    <!-- Left vertical platform label -->
                                    <div style="
                                        width: 28px;
                                        background-color: ${platformColor};
                                        color: white;
                                        font-weight: bold;
                                        font-size: 11px;
                                        writing-mode: vertical-rl;
                                        text-orientation: mixed;
                                        text-transform: uppercase;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        border-radius: 0 4px 4px 0;
                                    ">
                                        ${platform}
                                    </div>

                                    <!-- Main info -->
                                    <div style="flex: 1; padding-left: 10px; display: flex; flex-direction: column; justify-content: center;">
                                        <strong><a href="${serviceUrl}" target="_blank">${serviceName}</a></strong>
                                        <hr>
                                        <strong style="font-size: 14px;">${orderId}</strong>
                                        <small class="text-muted">${platform}</small>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: "order_created"
                    },
                    {
                        data: "date"
                    },
                    {
                        data: "package_price"
                    },
                    {
                        data: "wallet"
                    },
                    {
                        data: "online"
                    },
                    {
                        data: "coupon"
                    },
                    {
                        data: "amount"
                    },
                    {
                        data: "status",
                        render: function(data, type, row) {
                            const escapeHtml = (text) => {
                                return String(text ?? '')
                                    .replace(/&/g, "&amp;")
                                    .replace(/</g, "&lt;")
                                    .replace(/>/g, "&gt;")
                                    .replace(/"/g, "&quot;")
                                    .replace(/'/g, "&#039;");
                            };

                            // let serviceType = escapeHtml(row.service_type);
                            let leadId = escapeHtml(row.leadno);
                            let leadIcreamentId = escapeHtml(row.leadIcreamentId);
                            let personName = escapeHtml(row.person_name);
                            let personPhone = escapeHtml(row.person_phone);
                            let serviceName = escapeHtml(row.service_name);
                            let serviceId = escapeHtml(row.service_id);

                            return `
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <!-- Row 1 -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="${leadDeleteBaseUrl.replace(':leadno', leadId)}"
                                        class="btn btn-icon bg-label-danger waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        onclick="return confirm('Are you sure you want to delete?')"
                                        title="Delete">
                                        <i class="tio-delete-outlined" style="font-size:14px;"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                        class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        data-custId="${personName}"
                                        data-poojaId="${serviceId}"
                                        data-serviceName="${serviceName}"
                                        data-leadsId="${leadIcreamentId}"
                                        onclick="followUp(this)"
                                        title="Follow Up">
                                        <i class="tio-settings-back" style="font-size:14px;"></i>
                                        </a>
                                    </div>

                                    <!-- Row 2 -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="javascript:void(0)"
                                        class="btn btn-icon bg-label-info waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        data-leadsId="${leadIcreamentId}"
                                        onclick="followHistory(this)"
                                        title="Follow Up History">
                                        <i class="tio-history" style="font-size:14px;"></i>
                                        </a>
                                        <a href="tel:${personPhone}"
                                        class="btn btn-icon bg-label-warning waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        title="Call">
                                        <i class="tio-call" style="font-size:14px;"></i>
                                        </a>
                                    </div>

                                    <!-- Row 3 -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="${whatsappBaseUrl.replace(':leadno', leadId)}"
                                        class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        title="whatsapp">
                                        <i class="tio-whatsapp" style="font-size:14px;"></i>
                                        <span class="btn-status btn-sm-status btn-status-danger">
                                            ${row.whatsapp_hit ?? 0}
                                        </span>
                                        </a>
                                        <a href="${whatsappUpdateBaseUrl}"
                                        class="btn btn-icon bg-label-primary waves-effect waves-light myactionbtn"
                                        style="padding:2px 4px;" 
                                        title="customise message" target="_blank">
                                        <i class="tio-message" style="font-size:14px;"></i>
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }

                ],


                createdRow: function(row, data, dataIndex) {
                    let plainStatus = $('<div>').html(data.status).text().trim().toLowerCase();
                    if (plainStatus === 'complete') {
                        $(row).css('background-color', '#caffc6'); // Light green
                    }
                }
            });

            $('.start_date, .end_date, .service_id').on('change', function() {
                table.ajax.reload();
            });

            let searchDelay;
            $('#datatableSearch_').on('keyup', function() {
                clearTimeout(searchDelay);
                searchDelay = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });
            $('#filterBtn').on('click', function() {
                table.draw();
            });

            $('#resetBtn').on('click', function() {
                $('#filterDate').val('');
                $('#searchInput').val('');
                table.draw();
            });
        });
    </script>

    <script>
        function GenrateLeads(el) {
            $('#manulyLeads').modal('show'); // Show modal
        }
    </script>

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
            var poojaid = $(that).attr('data-PoojaId');
            // var type = $(that).attr('data-type');
            var lead = $(that).attr('data-leadsId');
            var serviceName = $(that).attr('data-serviceName');
            console.log(lead);
            $('#followUpLeadID').val(lead);
            $('#followUpUserId').val(id);
            $('#followUpPoojaId').val(poojaid);
            // $('#followUpType').val(type);
            $('#followUpServiceName').val(serviceName);
            $('#followUpModal').modal('show');
        }
    </script>
    <script>
        function followHistory(that) {
            var leadId = $(that).attr('data-leadsId');
            var types = $(that).attr('data-type');
            var row = "";
            $.ajax({
                url: "{{ url('admin/leads/offlinepooja/get-follows-list') }}/" + leadId,
                type: 'GET',
                //   data:{id:leadId},
                success: function(response) {
                    console.log(response);
                    $('#followUpPoojaHistoryTBody').html('');
                    if (response.length != 0) {
                        $.each(response, function(key, value) {
                            row +=
                                `<tr> <td>${key+1}</td> <td>${value.follow_by}</td> <td>${new Date(value.last_date).toLocaleDateString('en-GB')}</td> <td>${value.message}</td> <td>${new Date(value.next_date).toLocaleDateString('en-GB')}</td> </tr>`;
                        });
                    } else {
                        row = '<tr> <td colspan="5" class="text-center"> No Data Available </td> </tr>';
                    }
                    $('#followUpPoojaHistoryTBody').append(row);
                },
            });
            $('#followUpHistoryModal').modal('show');
        }
    </script>
@endpush
