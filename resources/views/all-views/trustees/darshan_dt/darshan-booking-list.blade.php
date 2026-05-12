@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')
@section('title', 'Darshan Booking List')
@push('css_or_js')
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="" style="height:40px;">
                Darshan List
            </h2>
            <div>
                <a href="{{ route('trustees-vendor.darshan-booking.darshan-booking') }}" class="btn btn-primary">Book
                    Now</a>
            </div>
        </div>
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-12">
                        <div class="row g-2">

                            <div class="col-md-3">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-success">
                                                ₹ {{ $razorPayAmount }} / -
                                            </h3>
                                            <div class="text-capitalize mb-0 text-success">Online Total Earning</div>
                                        </div>
                                        <div>
                                            <img width="40" class="mb-2 rotate-icon"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.chadhava.order.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-warning">
                                                    ₹ {{ $offlineAmount }} /-
                                                </h3>
                                                <div class="text-capitalize mb-0 text-warning">Cash Total Earning
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
                            <div class="col-md-3">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-success">
                                                ₹ {{ $paltformFeeAmount }} /-
                                            </h3>
                                            <div class="text-capitalize mb-0 text-success">Platform Total Earning</div>
                                        </div>
                                        <div>
                                            <img width="40" class="mb-2 rotate-icon"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.chadhava.order.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-warning">
                                                    ₹ {{ $recepintAmount }} /-
                                                </h3>
                                                <div class="text-capitalize mb-0 text-warning">Recepit Total Earning
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
                            <div class="col-md-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">{{ $totalOrders }}</h3>
                                                <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2 rotate-icon"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/pending.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">{{ $onlineOrders }}</h3>
                                                <div class="text-capitalize mb-0">TOTAL ORDER Online</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2 rotate-icon"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/online.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">{{ $offlineOrders }}</h3>
                                                <div class="text-capitalize mb-0">TOTAL ORDER Cash</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2 rotate-icon"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/cash-in-hand.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <!-- Heading + Button Row -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Filter the Order List</h4>
                        </div>
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET"
                                    action="{{ route('trustees-vendor.darshan-booking.get.bookingList') }}">
                                    <div class="row">
                                        {{-- Payment Status Filter --}}
                                        <div class="col-md-3">
                                            <label for="payment_status" class="font-weight-bold">Payment Status</label>
                                            <select name="payment_status" id="payment-status" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">All</option>
                                                <option value="Offline"
                                                    {{ request('payment_status') == 'Offline' ? 'selected' : '' }}>Cash
                                                </option>
                                                <option value="razor_pay"
                                                    {{ request('payment_status') == 'razor_pay' ? 'selected' : '' }}>Online
                                                    Pay
                                                </option>
                                            </select>
                                        </div>
                                        {{-- Package Name Filter --}}
                                        <div class="col-md-3">
                                            <label for="package_name" class="font-weight-bold">Package</label>
                                            <select name="package_name" id="package_name" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">All</option>
                                                @foreach ($packages as $package)
                                                    <option value="{{ $package->package_name }}"
                                                        {{ request('package_name') == $package->package_name ? 'selected' : '' }}>
                                                        {{ $package->package_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Purohit Name Filter --}}
                                        <div class="col-md-3">
                                            <label for="purohit_id" class="font-weight-bold">Purohit</label>
                                            <select name="purohit_id" id="purohit_id" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">All</option>
                                                @foreach ($purohits as $purohit)
                                                    <option value="{{ $purohit->id }}"
                                                        {{ request('purohit_id') == $purohit->id ? 'selected' : '' }}>
                                                        {{ $purohit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Month Wise Filter --}}
                                        <div class="col-md-3">
                                            <label for="month" class="font-weight-bold">Month</label>
                                            <select name="month" class="form-control" id="monthFilter">
                                                <option value="">Select Month</option>
                                                @for ($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}"
                                                        {{ request('month') == $m ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        {{-- Start Date --}}
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Start Date</label>
                                            <input type="date" name="from_date" class="form-control"
                                                value="{{ request('from_date') }}">
                                        </div>
                                        {{-- End Date --}}
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">End Date</label>
                                            <input type="date" name="to_date" class="form-control"
                                                value="{{ request('to_date') }}">
                                        </div>
                                        {{-- Filter Button --}}
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <table id="purohitpayment" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-light text-capitalize">
                                        <tr>
                                            <th>#</th>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Temple</th>
                                            <th>Purohit</th>
                                            <th>Member</th>
                                            <th>Package</th>
                                            <th>Price</th>
                                            <th>Receipt Price</th>
                                            <th>Platform Fee</th>
                                            <th>Final Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($darshanList as $key => $item)
                                            @php
                                                $membersCount = $item->members->count();
                                            @endphp
                                            @foreach ($item->members as $index => $member)
                                                <tr>
                                                    {{-- Sirf pehli row me common details dikhayenge --}}
                                                    @if ($index === 0)
                                                        <td rowspan="{{ $membersCount }}"
                                                            style="position: relative; padding-left: 40px;">
                                                            <div
                                                                style=" position: absolute; top: 50%; left: 0; transform: translateY(-50%); height: 60px;width: 28px;
                                    background-color: {{ $item->payment_method == 'Offline' ? '#28a745' : ($item->payment_method == 'razor_pay' ? '#dc3545' : '#6c757d') }}; display: flex;  align-items: center;   justify-content: center;  writing-mode: vertical-rl; text-orientation: mixed;  color: white; font-weight: bold; font-size: 10px; text-transform: uppercase;border-radius: 0 4px 4px 0;">
                                                                {{ $item->payment_method == 'Offline' ? 'Cash' : ($item->payment_method == 'razor_pay' ? 'Online' : '') }}
                                                            </div>
                                                            {{ $key + 1 }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">{{ $item->order_id }}</td>
                                                        <td rowspan="{{ $membersCount }}">{{ $item->date }}</td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->temple->name ?? '-' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->purohit->name ?? '-' }}
                                                        </td>
                                                    @endif
                                                    {{-- Members ke details --}}
                                                    <td>{{ $member->name }}</td>
                                                    @if ($index === 0)
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->title ?? '-' }}{{ $item->package_name ?? '-' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->price ?? '0' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->receipt_price ?? '0' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->platform_fee ?? '0' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->final_amount ?? '0' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}">
                                                            {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                                        </td>
                                                        <td rowspan="{{ $membersCount }}" class="text-center">
                                                            <button class="btn btn-sm btn-primary"
                                                                data-id="{{ $item->id }}"
                                                                data-purohit-id="{{ $item->purohit_id ?? '' }}"
                                                                data-temple-id="{{ $item->temple_id ?? '' }}"
                                                                onclick="openPanditModal(this)">
                                                                {{ $item->purohit_id ? 'Edit Purohit' : 'Assign Purohit' }}
                                                            </button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tfoot>
                                            <tr>
                                                <th>SL</th>
                                                <th>Order Id</th>
                                                <th>Create Order Date</th>
                                                <th>Temple Name</th>
                                                <th>Purohit Name</th>
                                                <th>Yajman Details</th>
                                                <th>Puja, VIP Darshan, Package Name</th>
                                                <th>Package Price</th>
                                                <th>Receipt Price</th>
                                                <th>Platform Fee</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </tfoot>

                                    </tfoot>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="assignPanditModal" tabindex="-1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignPanditLabel">Assign Pandit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="assign_order_id" name="id">
                        <div class="mb-3">
                            <label for="purohit-select" class="form-label">Select Pandit</label>
                            <select class="form-control" id="purohit-select">
                                <option value="">-- Select Pandit --</option>
                                @foreach ($purohits as $purohit)
                                    <option value="{{ $purohit->id }}" data-temple-id="{{ $purohit->temple_id }}">
                                        {{ $purohit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" onclick="savePandit()">Save</button>
                    </div>
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

                    // 🚀 Clear All Filters
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


        $('#payment-status').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $('#payment-mode').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $('#monthFilter').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
    </script>
    <script>
        // Assing the Pandit
        function openPanditModal(button) {
            let orderId = $(button).data("id");
            let currentPurohitId = $(button).data("purohit-id") || null;
            let templeId = $(button).data("temple-id") || null;
            $("#assign_order_id").val(orderId);
            // Filter Pandit dropdown by templeId
            $("#purohit-select option").each(function() {
                let optionTempleId = $(this).data("temple-id"); // you need to add data-temple-id to options
                if (!templeId || optionTempleId == templeId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $("#purohit-select").val(currentPurohitId || '');
            $("#assignPanditModal").modal('show');
        }

        function savePandit() {
            let id = $("#assign_order_id").val();
            let purohitId = $("#purohit-select").val();
            if (!purohitId) {
                alert("Please select a Pandit.");
                return;
            }
            $.ajax({
                url: "{{ route('trustees-vendor.darshan-booking.get.update-purohit') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    purohit_id: purohitId
                },
                success: function(response) {
                    if (response.success) {
                        $(`#purohit-name-${id}`).text(response.purohit_name);
                        $("#assignPanditModal").modal("hide");
                        toastr.success("Pandit updated successfully!", "Success");
                        $('#orderListtable').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error("Something went wrong!", "Error");
                    }
                }
            });
        }
    </script>
@endpush