@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')
@section('title', 'Puja Booking List')
@push('css_or_js')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
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
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">Puja Booking List
        </h2>
    </div>

    <div class="card">
        <div class="card-body">
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
                        <select class="puja_name form-control">
                            <?php $pujaname = \App\Models\TrustPuja::where('trust_id',$relationEmployees)->get(); ?>
                            <option value="">Select Puja Name</option>
                            @if($pujaname)
                            @foreach($pujaname as $val)
                            <option value="{{ $val['puja_name']}}">{{ $val['puja_name']}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="vipOrderTable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>SL.</th>
                                    <th>Order ID</th>
                                    <th>Customer Info</th>
                                    <th>Puja Name</th>
                                    <th>Booking Date</th>
                                    <th>Payment Platform</th>
                                    <th>Payment Summary</th>
                                    <th>Final Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

@push('script')
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        let table = $('#vipOrderTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ],
            dom: 'Blfrtip',
            ajax: {
                url: "{{ route('trustees-vendor.puja-management.puja-booking-filters') }}",
                type: "GET",
                data: function(d) {
                    d.start_date = $('.start_date').val();
                    d.end_date = $('.end_date').val();
                    d.puja_name = $('.puja_name').val();
                    d.searchValue = $('#datatableSearch_').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'order_id'
                },
                {
                    data: 'useinfo'
                },
                {
                    data: 'puja_name'
                },
                {
                    data: "date"
                },
                {
                    data: 'payment_paltform'
                },
                {
                    data: 'payment_summary'
                },
                {
                    data: 'final_amount'
                },
            ]
        });
        $('.start_date, .end_date, .puja_name').on('change', function() {
            table.ajax.reload();
        });
        let searchDelay;
        $('#datatableSearch_').on('keyup', function() {
            clearTimeout(searchDelay);
            searchDelay = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });
    });
</script>
@endpush