@extends('layouts.back-end.app-trustees')
@php use App\Utils\Helpers; @endphp

@section('title', translate('purohit-transaction-list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
                {{ translate('purohit-transaction-list') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
            </h2>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#temple-list">
                Add Pandit
            </button>
        </div>

        <div class="row mt-20">
            <div class="px-3 py-4">
                <div class="row g-3">
                    <!-- Online Total Earning -->
                    <div class="col-md-3">
                        <a href="{{ route('admin.chadhava.order.list', 'online') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-primary">
                                            ₹ {{ $onlineTotal }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-primary">
                                            Online Total Earning ({{ $onlineCount }} orders)
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/online.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Offline Total Earning -->
                    <div class="col-md-3">
                        <a href="{{ route('admin.chadhava.order.list', 'offline') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-warning">
                                            ₹ {{ $cashTotal }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-warning">
                                            Offline Total Earning ({{ $cashCount }} orders)
                                        </div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2 rotate-icon"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Pending Orders -->
                    <div class="col-md-3">
                        <a href="{{ route('admin.chadhava.order.list', 'pending') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-danger">
                                            {{ $pendingCount }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-danger">Pending Orders</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/pending.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Complete Orders -->
                    <div class="col-md-3">
                        <a href="{{ route('admin.chadhava.order.list', 'complete') }}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-success">
                                            {{ $completeCount }}
                                        </h3>
                                        <div class="text-capitalize mb-0 text-success">Complete Orders</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/complete.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>


            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <!-- Filters above the table -->
                                        <!-- Filters above the table -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label>Temple</label>
                                                <select class="form-control filter-temple"></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Purohit</label>
                                                <select class="form-control filter-purohit"></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Payment Method</label>
                                                <select class="form-control filter-method"></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Payment Status</label>
                                                <select class="form-control filter-status"></select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Order Date</label>
                                                <input type="text" class="form-control filter-date"
                                                    placeholder="Select Date">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Price Range</label>
                                                <div class="d-flex">
                                                    <input type="number" class="form-control filter-min" placeholder="Min"
                                                        style="width: 50%;">
                                                    <input type="number" class="form-control filter-max" placeholder="Max"
                                                        style="width: 50%;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Clear Filters Button -->
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-secondary clear-filters">Clear All
                                                Filters</button>
                                        </div>

                                        <table id="purohitpayment" class="table table-striped table-bordered table-hover">
                                            <thead class="thead-light text-capitalize">
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Temple</th>
                                                    <th>Date</th>
                                                    <th>Purohit</th>
                                                    <th>Price</th>
                                                    <th>Method</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $key => $item)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $item->temple->name }}</td>
                                                        <td>{{ $item->darshanOrder->date }}</td>
                                                        <td>{{ $item->purohit->name ?? 'N/A' }}</td>
                                                        <td>{{ $item->darshanOrder->price }}</td>
                                                        <td>{{ $item->payment_method }}</td>
                                                        <td>{{ $item->payment_status }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
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
    <span id="route-admin-rashi-status-update" data-url="{{ route('admin.temple.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    <script>
        $(document).ready(function() {
            let table = $('#purohitpayment').DataTable({
                pageLength: 10,
                scrollY: '500px',
                scrollCollapse: true,
                paging: true,
                fixedHeader: true,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                initComplete: function() {
                    let api = this.api();

                    // Dropdown filters (Temple, Purohit, Method, Status)
                    let filterColumns = {
                        '.filter-temple': 1,
                        '.filter-purohit': 3,
                        '.filter-method': 5,
                        '.filter-status': 6
                    };

                    $.each(filterColumns, function(selector, colIdx) {
                        let column = api.column(colIdx);
                        let select = $(selector);

                        select.empty().append('<option value="">All</option>');
                        column.data().unique().sort().each(function(d) {
                            if (d && d.trim() !== '') {
                                select.append('<option value="' + d + '">' + d +
                                    '</option>');
                            }
                        });

                        select.select2({
                            placeholder: "All",
                            allowClear: true,
                            width: '100%'
                        });

                        select.on('change', function() {
                            let val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });
                    });

                    // 📅 DATE FILTER (using datepicker)
                    $(".filter-date").datepicker({
                        dateFormat: "yy-mm-dd",
                        onSelect: function(dateText) {
                            api.column(2).search(dateText).draw(); // column index 2 = Date
                        }
                    });

                    // 💰 PRICE FILTER (Range)
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            let min = parseFloat($('.filter-min').val()) || 0;
                            let max = parseFloat($('.filter-max').val()) || Infinity;
                            let price = parseFloat(data[4]) || 0; // column index 4 = Price

                            if (price >= min && price <= max) {
                                return true;
                            }
                            return false;
                        }
                    );

                    $('.filter-min, .filter-max').on('keyup change', function() {
                        table.draw(); // redraw table when price changes
                    });

                    //  Clear All Filters
                    $('.clear-filters').on('click', function() {
                        $('.filter-temple, .filter-purohit, .filter-method, .filter-status')
                            .val('').trigger('change');
                        $('.filter-date').val('');
                        $('.filter-min, .filter-max').val('');

                        // clear searches
                        api.search('').columns().search('').draw();
                    });
                }
            });
        });
    </script>
@endpush