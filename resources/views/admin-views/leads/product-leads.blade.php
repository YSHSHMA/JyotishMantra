@extends('layouts.admin.app')

@section('title', 'Product Leads')

@push('css_or_js')
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .bg-panditpooja { background-color: #007bff; }
        .bg-pooja { background-color: #007bff; }
        .bg-chadhava { background-color: #28a745; }
        .bg-vip { background-color: #ffc107; }
        .bg-anushthan { background-color: #ff9800; }
    </style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-3">
        <h1 class="page-header-title">
            Product Leads
        </h1>
    </div>

    @php
        $total_price = $productLeads->sum('final_price');
    @endphp
    @php
        $completed_payment_total = $productLeads
            ->filter(fn ($item) =>
                $item->lead &&
                $item->lead->payment_status === 'Complete'
            )
            ->sum('final_price');
    @endphp
    @php
        // Pending payments total
        $pending_payment_total = $productLeads
            ->filter(fn($item) => $item->lead && $item->lead->payment_status === 'pending')
            ->sum('final_price');
    @endphp
    @php
        $leadTypeColors = [
            'panditpooja' => '#007bff',       // Blue
            'chadhava' => '#28a745', // Green
            'vip' => '#ffc107',         // Yellow
            'anushthan' => '#ff9800',   // Orange
        ];
    @endphp

    {{-- First Row: 3 cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card card-body h-100 justify-content-center shadow-sm rounded-lg border-0">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-success">₹ {{ number_format($total_price, 2) }}</h3>
                        <div class="mb-0 text-success">Total Price</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset('public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card card-body h-100 justify-content-center shadow-sm rounded-lg border-0">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-success">₹ {{ number_format($completed_payment_total, 2) }}</h3>
                        <div class="mb-0 text-success">Completed Payments</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset('public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card card-body h-100 justify-content-center shadow-sm rounded-lg border-0">
                <div class="d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fz-24 text-warning">₹ {{ number_format($pending_payment_total, 2) }}</h3>
                        <div class="mb-0 text-warning">Pending Payments</div>
                    </div>
                    <img width="40" src="{{ dynamicAsset('public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>

    {{-- Second Row: 4 cards --}}
    <div class="row g-3 mb-4">
        @foreach ($data as $type => $count)
            @php
                $icon = match($type) {
                    'pooja' => 'public/assets/back-end/img/pooja/panditpooja.png',
                    'counselling' => 'public/assets/back-end/img/pooja/pandit_counselling.webp',
                    'vip' => 'public/assets/back-end/img/pooja/panditpooja.png',
                    'anushthan' => 'public/assets/back-end/img/pooja/chadhava.webp',
                    'chadhava' => 'public/assets/back-end/img/pooja/chadhava.webp',
                    default => 'public/assets/back-end/img/pooja/rupay.png',
                };
            @endphp

            <div class="col-12 col-md-3">
                <div class="card card-body h-100 justify-content-center shadow-sm rounded-lg border-0">
                    <div class="d-flex gap-2 justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fz-24 text-primary">{{ $count }}</h3>
                            <div class="mb-0 text-capitalize">{{ $type }}</div>
                        </div>
                        <img width="50" src="{{ dynamicAsset($icon) }}" alt="{{ $type }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    {{-- ===================== LEAD TYPE FILTER ===================== --}}
    <div class="card mb-3">
        <div class="px-3 py-4">
            <h4 class="mb-3">Filter Product Leads</h4>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="font-weight-bold">Lead Type</label>
                    <select id="leadTypeFilter" class="form-control">
                        <option value="">All Lead Types</option>
                        <option value="panditpooja">Pooja</option>
                        <option value="vip">VIP</option>
                        <option value="anushthan">Anushthan</option>
                        <option value="chadhava">Chadhava</option>
                    </select>
                </div>

                <!-- <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" id="clearFilter">
                        Clear Filter
                    </button>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body table-responsive">
            <!-- Legend for Lead Types -->
            <div class="d-flex flex-wrap gap-3 mt-2 mb-4">
                @foreach ($leadTypeColors as $type => $color)
                    <div class="d-flex align-items-center gap-1">
                        <span style="width: 15px; height: 15px; background-color: {{ $color }}; display: inline-block; border-radius: 3px;"></span>
                        <small class="text-capitalize">{{ $type }}</small>
                    </div>
                @endforeach
            </div>

            <table  id="table" class="table table-bordered table-hover table-align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Lead ID</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Final Price</th>
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productLeads as $key => $lead)
                        @php
                            $type = optional($lead->lead)->type ?? '';
                            $bgColor = $leadTypeColors[$type] ?? '';
                            $textColor = in_array($type, ['vip', 'anushthan']) ? '#000' : '#fff';
                        @endphp
                            <!-- <tr data-lead-type="{{ $lead->lead->type ?? '' }}" > -->
                            <tr style="background-color: {{ $bgColor }}; color: {{ $textColor }};" data-lead-type="{{ $type }}">
                                <td>{{ $productLeads->firstItem() + $key }}</td>

                                <td>
                                    {{ $lead->lead->order_id ?? '-' }}
                                </td>

                                <td>
                                    {{ $lead->product_name }}
                                    <span class="text">
                                        ({{ $lead->product_id }})
                                    </span>
                                </td>

                                <td>
                                    ₹ {{ $lead->product_price }} × {{ $lead->qty }}
                                </td>

                                <td>
                                    <strong>₹ {{ $lead->final_price }}</strong>
                                </td>

                                <td>
                                    {{ optional($lead->lead)->booking_date
                                        ? optional($lead->lead)->booking_date->format('d M Y')
                                        : '-' }}
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No Product Leads Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
        let table = $('#table').DataTable({
          pageLength: 20,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
        $('#payment_status').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
</script>

<script>
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

        let selectedType = $('#leadTypeFilter').val();

        if (!selectedType) {
            return true;
        }

        let table = $('#table').DataTable();
        let rowNode = table.row(dataIndex).node();
        let rowType = $(rowNode).data('lead-type');

        return rowType === selectedType;
    });

    // Apply filter on change
    $('#leadTypeFilter').on('change', function () {
        $('#table').DataTable().draw();
    });

    //  Clear filter
    $('#clearFilter').on('click', function () {
        $('#leadTypeFilter').val('');
        $('#table').DataTable().draw();
    });
</script>

@endpush