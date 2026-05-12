@extends('layouts.back-end.app')

@section('title', translate('Tour_order_list'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
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
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Tour_order_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ $getData->total() }}</span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('User_Info') }}</th>
                                    <th>{{ translate('tour_Info') }}</th>
                                    <th>{{ translate('amount') }}</th>
                                    <th>{{ translate('coupon_amount') }}</th>
                                    <th class="text-center">{{ translate('gst_amount') }}</th>
                                    <th class="text-center">{{ translate('admin_commission') }}</th>
                                    <th class="text-center">{{ translate('final_amount') }}</th>
                                    <th class="text-center"> {{ translate('TXN_ID') }}</th>
                                    <th class="text-center"> {{ translate('option') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getData as $key => $lead)
                                <tr>
                                    <td>{{ $getData->firstItem()+$key }}</td>
                                    <td>
                                        <div>
                                            <small>{{ ($lead['userData']['name']??"") }}</small><br>
                                            <small>{{ ($lead['userData']['phone']??"") }}</small><br>
                                            <span>qty: {{ $lead['qty'] }}</span><br>
                                    <span>package :
                                        @if(!empty($lead['Tour']['package_list']) && json_decode($lead['Tour']['package_list'],true))
                                        @foreach(json_decode($lead['Tour']['package_list'],true) as $val)
                                        @if($val['id'] == $lead['package_id'])
                                        {{ (\App\Models\TourCab::where('id',$val['cab_id'])->first()['name']??"") }}
                                        <a role='tooltip' data-toggle="tooltip" data-html="true" title="
                                        @if(!empty($val['package_id']??''))
                                        @foreach($val['package_id'] as $pn)
                                        <p>Package added : <strong>{{ (\App\Models\TourPackage::where('id',($pn??''))->first()['name']??'') }}</strong></p>
                                        @endforeach 
                                        @endif
                                        ">
                                        <i class="tio-info"></i>
                                    </a>
                                        @endif
                                        @endforeach
                                        @endif
                                    </span><br>
                                            <small>{{ date('d M,Y h:i A',strtotime($lead['created_at']??"")) }}</small><br>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small>{{ $lead['pickup_date']}} {{$lead['pickup_time']}}</small>
                                            <p class="font-weight-bold" data-title="{{($lead['Tour']['tour_name']??'')}}" role='tooltip' data-toggle='tooltip'>{{ Str::limit(($lead['Tour']['tour_name']??""),20) }}</p>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['amount'] + $lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                    <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                    <td class="text-center">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['gst_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                    <td class="text-center"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['admin_commission'])), currencyCode: getCurrencyCode()) }}</td>
                                    <td class="text-center"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['final_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                    <td class="text-center"> {{ ($lead['transaction_id']) }}</td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-info" href="{{ route('admin.tour-visits-booking.user-booking-details',[$lead['id']]) }}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a class="btn btn-sm btn-info" onclick="$('.update-date-model').modal('show');$('.old-date-time-model').text(`{{ $lead['pickup_date']}} {{$lead['pickup_time']}}`);$('.update-date-model-form')[0].reset();$('.order_id-date-update').val(`{{ ($lead['id']) }}`);">
                                            <i class="tio-event"></i>
                                        </a>
                                        <a class="btn btn-sm btn-warning text-white" onclick="onhistory(this)" data-id="{{ ($lead['id']) }}" data-method="{{ ($lead['payment_method']) }}" data-transaction="{{ ($lead['transaction_id']) }}" data-status="{{ ($lead['refound_id']) }}" data-amount="{{ ($lead['amount'] + $lead['coupon_amount']) }}">
                                            <i class="tio-autorenew"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $getData->links() }}
                    </div>
                </div>
                @if(count($getData)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



<div class="modal fade update-date-model" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" class="update-date-model-form" autocomplete="off" action="{{ route('admin.tour-visits-booking.update-booking-date')}}">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6 mt-2">
                            <label for="form-label" class="font-weight-bold">Old Booking Date</label>
                        </div>
                        <div class='col-md-6 mt-2'>
                            <span class="old-date-time-model font-weight-bold"></span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Enter Refund Amount</label>
                            <input type="text" class="form-control bookingdate" name="date" require>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="">Enter Refund Amount</label>
                            <input type="text" class="form-control opentime" name="time" require>
                            <input type="hidden" class="form-control order_id-date-update" name="id" require>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--  -->
<div class="modal fade refund_pay" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateTimeModalLabel">Refund Pay</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" autocomplete="off" action="{{ route('admin.tour-visits-booking.refund')}}">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-12 mt-2">
                            <label for="">Select Type</label>
                            <select name="type" require class="form-control">
                                <option value="">select option</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Payment Method</label>
                            <input type="hidden" class="form-control refund_tour_id" name="id" value="">
                            <input type="text" class="form-control refund_tour_pay_method" name="payment_method" value="" readonly>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Old Transaction Id</label>
                            <input type="text" class="form-control refund_tour_pay_transaction" name="transaction_id" readonly>
                            <input type="hidden" class="form-control refund_tour_pay_status" name="refund_id">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Pay Amount</label>
                            <input type="text" class="form-control refund_tour_pay_amount" name="amount" value="" readonly>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Enter Refund Amount</label>
                            <input type="text" class="form-control" name="refund_amount" value="0" require>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Refund Now</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
</script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script>
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
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    function onhistory(that) {
        $(".refund_pay").modal('show');
        $(".refund_tour_id").val($(that).data('id'));
        $(".refund_tour_pay_method").val($(that).data('method'));
        $(".refund_tour_pay_transaction").val($(that).data('transaction'));
        $(".refund_tour_pay_status").val($(that).data('status'));
        $(".refund_tour_pay_amount").val($(that).data('amount'));
    }

    $('.opentime').timepicker({
        uiLibrary: 'bootstrap4',
        format: 'hh:MM TT', // Correct format for time display (12-hour with AM/PM)
        modal: true,
        footer: true
    });
    date_format_picker();

    function date_format_picker() {
        let today = new Date();
        let tomorrow = new Date(today);
        tomorrow.setDate(today.getDate());
        $('.bookingdate').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            modal: true,
            footer: true,
            minDate: tomorrow,
            todayHighlight: true
        });
    }
</script>
@endpush