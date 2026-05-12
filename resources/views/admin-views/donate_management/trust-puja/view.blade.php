@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', 'Puja Booking List')
@push('css_or_js')

@endpush
@section('content')
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
                        <select class="trust_id form-control">
                            <option value="">Select Trust Name</option>
                            @if($trustList)
                            @foreach($trustList as $val)
                            @if($val['name'])
                            <option value="{{ $val['id']}}">{{ $val['trust_name']}}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="input-group input-group-custom input-group-merge">
                        <select class="puja_name form-control">
                            <?php $pujaname = \App\Models\TrustPuja::all(); ?>
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
                                    <th>Trust Info</th>
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
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#vipOrderTable',
            ajaxUrl: "{{ route('admin.donate_management.trustees-puja-booking.trust-puja-booking-filter') }}",
            exportTitle: "Trust Puja Orders",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                }, // serial no
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'useinfo',
                    name: 'useinfo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'trust_name',
                    name: 'trust_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'puja_name',
                    name: 'puja_name'
                },
                {
                    data: 'payment_paltform',
                    name: 'payment_paltform'
                },
                {
                    data: 'payment_summary',
                    name: 'payment_summary',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'final_amount',
                    name: 'final_amount',
                    render: function(data) {
                        return `₹${parseFloat(data).toFixed(2)}`;
                    }
                }
            ],
            extraOptions: {
                serverSide: true,
                ajax: {
                    data: function(d) {
                        d.searchValue = $('#datatableSearch_').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.trust_id = $('.trust_id').val();
                        d.puja_name = $('.puja_name').val();
                    }
                }
            }
        });
    });

    $('.start_date, .end_date, .trust_id, .puja_name').on('change', function() {
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