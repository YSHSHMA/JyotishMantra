@extends('layouts.back-end.app')

@section('title', translate('visitor_list'))

@section('content')
<div class="content container-fluid">

    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('visitor_list') }}
        </h2>
    </div>

    <!-- Filter Section -->
    <div class="card p-3 mb-3">
    <h5 class="mb-3">Search Visitors</h5>

    <!-- Top Filters: IP, Country, City -->
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label for="filter_ip">IP Address</label>
            <input type="text" id="filter_ip" class="form-control" placeholder="Enter IP">
        </div>
        <div class="col-md-3">
            <label for="filter_country">Country</label>
            <input type="text" id="filter_country" class="form-control" placeholder="Enter Country">
        </div>
        <div class="col-md-3">
            <label for="filter_city">City</label>
            <input type="text" id="filter_city" class="form-control" placeholder="Enter City">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" id="searchDetailsBtn">Search</button>
        </div>
    </div>

    <!-- Filter Type Radios -->
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter_type" value="date" checked>
            <label class="form-check-label">By Date</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter_type" value="month">
            <label class="form-check-label">By Month</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter_type" value="last_6_months">
            <label class="form-check-label">Last 6 Months</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter_type" value="year">
            <label class="form-check-label">Year</label>
        </div>
    </div>

    <!-- Date / Month / Year Filters -->
    <div class="row g-2 align-items-end" id="filter-fields">
        <!-- From Date -->
        <div class="col-md-3 filter-by-date">
            <label for="from_date">From</label>
            <input type="date" id="from_date" class="form-control">
        </div>

        <!-- To Date -->
        <div class="col-md-3 filter-by-date">
            <label for="to_date">To</label>
            <input type="date" id="to_date" class="form-control">
        </div>

        <!-- Month Dropdown -->
        <div class="col-md-3 filter-by-month" style="display: none;">
            <label for="filter_month">Month</label>
            <select id="filter_month" class="form-control">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </div>

        <!-- Year Dropdown -->
        <div class="col-md-3 filter-by-month filter-by-year" style="display: none;">
            <label for="filter_year">Year</label>
            <select id="filter_year" class="form-control">
                @for ($y = now()->year; $y >= 2015; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Apply & Reset Buttons -->
        <div class="col-md-6 d-flex gap-2 mt-2">
            <button class="btn btn-primary" id="filterBtn">Apply Filter</button>
            <button class="btn btn-secondary" id="resetBtn">Reset</button>
        </div>
    </div>
</div>


    <!-- Visitor Table -->
    <div class="card">
        <div class="px-3 pb-4">
            <div class="table-responsive">
                <table id="table" class="table table-bordered table-hover w-100">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date & Time</th>
                            <th>IP Address</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Referer</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function toggleFilters() {
        const filter = $('input[name="filter_type"]:checked').val();

        // Hide all filter fields by default
        $('.filter-by-date, .filter-by-month, .filter-by-year').hide();

        if (filter === 'date') {
            $('.filter-by-date').show();
        } else if (filter === 'month') {
            $('.filter-by-month, .filter-by-year').show();
        } else if (filter === 'year') {
            $('.filter-by-year').show();
        }

        // Always show the button group
        $('#filter-fields').find('.col-md-6').show();
    }

    let table;
    function loadTable() {
    table = $('#table').DataTable({
        processing: true,
        serverSide: false,
        destroy: true,
        dom: 'lBfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'], 
        ajax: {
            url: '{{ route("admin.visitor.visitor-data") }}',
            data: function (d) {
                d.filter_type = $('input[name="filter_type"]:checked').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.filter_month = $('#filter_month').val();
                d.filter_year = $('#filter_year').val();

                // New filters
                d.filter_ip = $('#filter_ip').val();
                d.filter_country = $('#filter_country').val();
                d.filter_city = $('#filter_city').val();
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                title: 'SL'
            },
            { data: 'created_at', title: 'Date & Time' },
            { data: 'ip_address', title: 'IP Address' },
            { data: 'country', title: 'Country' },
            { data: 'city', title: 'City' },
            { data: 'referer', title: 'Referer' },
            { data: 'url', title: 'URL' },
        ]
    });
}

$(document).ready(function () {
    loadTable();
    toggleFilters();

    $('input[name="filter_type"]').on('change', function () {
        toggleFilters();
    });

    $('#filterBtn, #searchDetailsBtn').on('click', function () {
        table.ajax.reload();
    });

    $('#resetBtn').on('click', function () {
        // Reset all filters
        $('#from_date, #to_date, #filter_ip, #filter_country, #filter_city').val('');
        $('#filter_month').val('{{ now()->month }}');
        $('#filter_year').val('{{ now()->year }}');
        $('input[name="filter_type"][value="date"]').prop('checked', true);
        toggleFilters();
        table.ajax.reload();
    });
});

</script>
@endpush
