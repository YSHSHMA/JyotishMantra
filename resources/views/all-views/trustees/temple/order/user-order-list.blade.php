@extends('layouts.back-end.app-trustees')
@section('title', translate('temple_order_list'))
@section('content')
@php
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$roleTabs = 1;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$roleTabs = 0;
} elseif (auth('purohit')->check()) {
$roleTabs=1;
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="px-3 py-4">
                    <!-- Heading + Button Row -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Order List</h4>
                    </div>
                    <div class="row g-2 flex-grow-1">
                        <?php $getTemples = \App\Models\Temple::where('trust_id', $relationEmployees)
                            ->when((auth('purohit')->check()), function ($q) {
                                $q->where('id', auth('purohit')->user()->temple_id);
                            })
                            ->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                                $q->where('id', auth('trust_employee')->user()->temple_id);
                            })
                            ->get();
                        $vendorEmp = \App\Models\VendorEmployees::where('type', "trust")->where('relation_id', $relationEmployees)->first();
                        $decoded = [];
                        if ($vendorEmp && $vendorEmp->selected_services) {
                            $raw = $vendorEmp->selected_services ?? '[]';
                            $firstDecode = json_decode($raw, true);
                            if (is_string($firstDecode)) {
                                $decoded = json_decode($firstDecode, true);
                            } else {
                                $decoded = $firstDecode;
                            }
                        }
                        ?>
                        @if($getTemples)
                        @foreach ($getTemples as $planT)
                        <div class="col-sm-12">
                            <span class="d-flex align-items-center text-capitalize gap-10 mb-0" style="text-decoration:underline;font-size: 18px;">
                                {{ $planT['name']}}
                            </span>
                        </div>
                        @if($planT['package_service'] && json_decode($planT['package_service']??"[]",true))
                        @foreach(json_decode($planT['package_service']??"[]",true) as $plan)
                        <?php
                        if (in_array($plan['name'], $decoded)) {
                            $roleTabs2 = 1;
                        } else {
                            $roleTabs2 = 0;
                        }
                        ?>
                        @if ($plan['status'] == 1 && ($roleTabs2 == 1 || $roleTabs == 1) && ((auth('trust')->check()) || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") != 'Sub Pandit')) || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit') && $plan['name'] == 'puja') || (auth('purohit')->check() && $plan['name'] == 'puja')))
                        <div class="col-sm-6 col-lg-3">
                            <a class="order-stats order-stats_pending">
                                <?php
                                $verifiedCount = 0;
                                $unverifiedCount = 0;

                                \App\Models\TempleOrderDetails::where('type', $plan['name'])
                                    ->where('payment_status', 1)
                                    ->where('trust_id', $relationEmployees)
                                    ->when((auth('purohit')->check()), function ($query) {
                                        return $query->where(['purohit_id' => auth('purohit')->user()->id]);
                                    })
                                    ->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($query) {
                                        return $query->where(['purohit_id' => auth('trust_employee')->user()->purohit_id])->where(['emp_id' => auth('trust_employee')->user()->id]);
                                    })
                                    ->get()
                                    ->each(function ($item) use (&$verifiedCount, &$unverifiedCount) {
                                        $customers = json_decode($item->customers, true) ?: [];
                                        foreach ($customers as $customer) {
                                            $status = $customer['verify_status'] ?? '0';
                                            if ($status == '1') {
                                                $verifiedCount++;
                                            } else {
                                                $unverifiedCount++;
                                            }
                                        }
                                    });

                                $totalCount = $verifiedCount + $unverifiedCount;
                                ?>
                                <div class="order-stats__content">
                                    <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/pending.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{ $plan['name'] }}({{ $totalCount }}) </h6>
                                </div>
                                <span class="order-stats__title" style="font-size: 12px;"> Verify: {{$verifiedCount}} <br>Pending: {{$unverifiedCount}}</span>
                            </a>
                        </div>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        @endif
                    </div>
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <select class="form-control payment_mode">
                                <option value="">Select Payment Method</option>
                                <option value="online">Online</option>
                                <option value="cash">Cash</option>
                                <option value="free">Free</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 d-none datebetweenOrderList">
                            <div class="input-group input-group-custom input-group-merge">
                                <input type="datetime-local" class="form-control start_date" value="{{ date('Y-m-d\TH:i') }}">
                                <input type="datetime-local" class="form-control end_date">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <span class="TodayOrderList btn btn-success">Today /Custom date</span>
                            <span class="TodayOrderListdnone btn btn-outline-success d-none">Today Order</span>
                        </div>

                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table id="orderlistpayment" class="table table-striped table-bordered table-hover">
                            <thead class="thead-light  text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('order_id') }}</th>
                                    <th>{{ translate('temple_name') }}</th>
                                    <th>{{ translate('Customer Name') }}</th>
                                    <th>{{ translate('pandit_amount') }}</th>
                                    <th>{{ translate('trust_amount') }}</th>
                                    <th>{{ translate('gst') }}</th>
                                    <th>{{ translate('platform_fee') }}</th>
                                    <th>{{ translate('amount') }}</th>
                                    <th>{{ translate('platform') }}</th>
                                    <th>{{ translate('payment_mode') }}</th>
                                    <th>{{ translate('create_date') }}</th>
                                    <th>{{ translate('Booking_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align: left;">Summary:</th>
                                    <th style="text-align: right;"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="detailsModalLabel">Customer Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#orderlistpayment',
            ajaxUrl: "{{ route('trustees-vendor.recepit-management.order-list-booking-filter') }}",
            exportTitle: "Trust Puja Orders",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'temple_name',
                    name: 'temple_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'yajman_name',
                    name: 'yajman_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pandit_amount',
                    name: 'pandit_amount',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'trust_amount',
                    name: 'trust_amount',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'gst',
                    name: 'gst',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'platform_fee',
                    name: 'platform_fee',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'amount',
                    name: 'amount',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'platform',
                    name: 'platform',
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                },
                {
                    data: 'create_by',
                    name: 'create_by',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'service',
                    name: 'service',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    if (json && json.footerData) {
                        $(api.column(3).footer()).html(
                            '<small><h4>' + (json.footerData.serviceSummary || 'No services') + '</h4><br></small>'
                        );
                        $(api.column(4).footer()).html(
                            '<strong>Total: ' + json.footerData.totalPanditAmount + '</strong>'
                        );
                        $(api.column(5).footer()).html(
                            '<strong>Total: ' + json.footerData.totalTrustAmount + '</strong>'
                        );
                        $(api.column(6).footer()).html(
                            '<strong>Total: ' + json.footerData.totalGstAmount + '</strong>'
                        );
                        $(api.column(7).footer()).html(
                            '<strong>Total: ' + json.footerData.totalPlatformAmount + '</strong>'
                        );
                        $(api.column(8).footer()).html(
                            '<strong>Total: ' + json.footerData.totalAmount + '</strong>'
                        );
                    }
                },
                initComplete: function() {
                    console.log('DataTable initialized successfully');
                },
                ajax: {
                    data: function(d) {
                        d.searchValue = $('#datatableSearch_').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.payment_mode = $('.payment_mode').val();
                        d.puja_name = $('.puja_name').val();
                    }
                }
            }
        });
    });

    $('.payment_mode, .start_date, .end_date').on('change', function() {
        $('#orderlistpayment').DataTable().draw();
    });

    $(document).ready(function() {
        $('.TodayOrderList').click(function() {
            $('.TodayOrderListdnone').removeClass('d-none');
            $('.datebetweenOrderList').removeClass('d-none');
            $('.TodayOrderList').addClass('d-none');
            $('.start_date').val("");
            $('#orderlistpayment').DataTable().draw();
        });
        $('.TodayOrderListdnone').click(function() {
            $('.datebetweenOrderList').addClass('d-none');
            $('.TodayOrderList').removeClass('d-none');
            $('.TodayOrderListdnone').addClass('d-none');
            $('.start_date').val("{{ date('Y-m-d\TH:i') }}");
            $('#orderlistpayment').DataTable().draw();
        });
    });

    $(document).on('click', '.view-details', function() {
        let htmlContent = $(this).data('html');

        $('#detailsModalBody').html(htmlContent);
        $('#detailsModal').modal('show');
    });
</script>
@endpush