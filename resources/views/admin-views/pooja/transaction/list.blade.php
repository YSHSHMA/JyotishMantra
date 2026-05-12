@extends('layouts.admin.app')

@push('css_or_js')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    /* Agar hidden karne ko use karenge filtering ke liye */
    .d-none {
      display: none !important;
    }
</style>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp

@php
$cards = [
    [
        'type' => 'pooja',
        'label' => 'Pooja',
        'color' => 'text-success',
        'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
    ],
    [
        'type' => 'vip',
        'label' => 'VIP',
        'color' => 'text-success',
        'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
    ],
    [
        'type' => 'anushthan',
        'label' => 'Anushthan',
        'color' => 'text-primary',
        'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
    ],
    [
        'type' => 'counselling',
        'label' => 'Counselling',
        'color' => 'text-danger',
        'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
    ],
];
@endphp

<div class="content container-fluid">

    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ asset('public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('Puja Transaction Records') }}
        </h2>
    </div>

    <!-- Total Cards -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                @foreach($cards as $card)
                <div class="col-md-3">
                    <div class="card card-body h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1 {{ $card['color'] }}">
                                    {{ \App\Models\PanditTransectionPooja::where('type', $card['type'])->count() }}
                                </h3>
                                <div>{{ $card['label'] }}</div>
                            </div>
                            <div>
                                <img width="40" src="{{ $card['image'] }}" alt="{{ $card['label'] }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label for="filterType" class="font-weight-bold">Service Type</label>
                    <select id="filterType" class="form-control">
                        <option value="">All Types</option>
                        <option value="pooja">Pooja</option>
                        <option value="vip">VIP</option>
                        <option value="anushthan">Anushthan</option>
                        <option value="counselling">Counselling</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="filterService" class="font-weight-bold">Service Name</label>
                    <select id="filterService" class="form-control">
                        <option value="">All Services</option>
                        {{-- Options will be populated by JS --}}
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="filterDate" class="font-weight-bold">Booking Date</label>
                    <input type="date" id="filterDate" class="form-control"/>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button id="clearFilters" class="btn btn-secondary w-100">
                        Clear All Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card p-4">
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 60px;">SL</th>
                    <th style="width: 100px;">Order ID</th>
                    <th style="width: 150px;">Pandit Name</th>
                    <th style="width: 180px;">Service Name</th>
                    <th style="width: 120px;">Booking Date</th>
                    <th style="width: 120px;">Order Amount</th>
                    <th style="width: 120px;">Commission</th>
                    <th style="width: 120px;">Pandit Share</th>
                    <th style="width: 120px;">Govt Tax</th>
                    <th style="width: 80px;">Type</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach ($transactions as $index => $t)
                <tr data-type="{{ $t->type }}"
                    data-service="{{ $t->type == 'vip' ? ($t->vipPooja->name ?? '') : ($t->service->name ?? '') }}"
                    data-date="{{ $t->booking_date }}">
                    <td>{{ $index + 1 }}</td>
                    <td
                        @php
                            $oid = $t->service_order_id ?? '';
                            $bg = '';
                            $text = '';
                            if ($oid !== '') {
                                if (Str::startsWith($oid, 'APJ')) {
                                    $bg = '#c5f3c1';
                                    $text = '#0b6b2f';
                                } elseif (Str::startsWith($oid, 'PJ')) {
                                    $bg = '#dff3e8';
                                    $text = '#166534';
                                } elseif (Str::startsWith($oid, 'CL')) {
                                    $bg = '#d1d2ff';
                                    $text = '#23238f';
                                } elseif (Str::startsWith($oid, 'VPJ')) {
                                    $bg = '#fff3b0';
                                    $text = '#856000';
                                }
                            }
                        @endphp
                        @if($bg)
                            style="background-color: {{ $bg }}; color: {{ $text }};"
                        @endif
                    >
                        {{ $oid ?: '-' }}
                    </td>
                    <td>{{ $t->pandit->name ?? 'Not Found' }}</td>
                    <td>
                        @if($t->type == 'vip')
                            <h6 class="mb-0">{{ $t->vipPooja->name ?? 'VIP Pooja' }}</h6>
                        @else
                            <h6 class="mb-0">{{ $t->service->name ?? 'General Pooja' }}</h6>
                        @endif
                        <small class="text-muted">{{ $t->type }}</small>
                    </td>
                    <td>{{ $t->booking_date }}</td>
                    <td>{{ number_format($t->order_amount, 2) }}</td>
                    <td>{{ number_format($t->admin_commission, 2) }}</td>
                    <td>{{ number_format($t->pandit_amount, 2) }}</td>
                    <td>{{ number_format($t->govt_tax, 2) }}</td>
                    <td style="text-transform: capitalize;">
                        {{ $t->type ?? 'N/A' }}
                    </td>
                </tr>
                @endforeach

                @if($transactions->count() == 0)
                <tr>
                    <td colspan="10" class="text-center">No records found</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {

        // Initialize datatable
        const dataTable = $('#table').DataTable({
            pageLength: 10,
            scrollX: true,
            autoWidth: false,
        });

        // ---- Collect All Table Data for Filtering ---- //
        const allRows = [];
        $('#tableBody tr').each(function() {
            allRows.push({
                row: $(this),
                type: $(this).data('type'),
                service: $(this).data('service'),
                date: $(this).data('date'),
            });
        });

        // ---- Populate All Services Initially ---- //
        function populateServiceDropdown(services) {
            $('#filterService').empty();
            $('#filterService').append(`<option value="">All Services</option>`);
            services.forEach(s => {
                $('#filterService').append(`<option value="${s}">${s}</option>`);
            });
        }

        // ---- Get All Unique Services From Table ---- //
        function getAllServices() {
            const srvSet = new Set();
            allRows.forEach(r => {
                if (r.service) srvSet.add(r.service);
            });
            return [...srvSet];
        }

        // Initial Load
        populateServiceDropdown(getAllServices());


        // ------ MAIN FILTER FUNCTION ------ //
        function applyFilters() {

            const selectedType = $('#filterType').val();
            const selectedService = $('#filterService').val();
            const selectedDate = $('#filterDate').val();

            allRows.forEach(r => {

                let show = true;

                if (selectedType && r.type !== selectedType) show = false;
                if (selectedService && r.service !== selectedService) show = false;
                if (selectedDate && r.date !== selectedDate) show = false;

                if (show) {
                    r.row.removeClass('d-none');
                } else {
                    r.row.addClass('d-none');
                }
            });

            dataTable.draw();
        }

        // ------ AUTO UPDATE SERVICE NAME WHEN TYPE SELECTED ------ //
        $('#filterType').on('change', function() {
            const selectedType = $(this).val();

            if (selectedType) {
                const services = new Set();
                allRows.forEach(r => {
                    if (r.type === selectedType && r.service) services.add(r.service);
                });
                populateServiceDropdown([...services]);
            } else {
                populateServiceDropdown(getAllServices());
            }

            applyFilters();
        });

        // ------ AUTO UPDATE TYPE WHEN SERVICE SELECTED ------ //
        $('#filterService').on('change', function() {

            const selectedService = $(this).val();

            if (selectedService) {
                let matchedType = '';

                allRows.forEach(r => {
                    if (r.service === selectedService) {
                        matchedType = r.type;
                    }
                });

                if (matchedType) {
                    $('#filterType').val(matchedType);
                }
            }

            applyFilters();
        });

        // ------ DATE FILTER ------ //
        $('#filterDate').on('change', applyFilters);

        // ------ CLEAR ALL FILTERS ------ //
        $('#clearFilters').on('click', function(e) {
            e.preventDefault();
            $('#filterType').val('');
            $('#filterService').val('');
            $('#filterDate').val('');
            populateServiceDropdown(getAllServices());
            applyFilters();
        });

    });
</script>

@endpush