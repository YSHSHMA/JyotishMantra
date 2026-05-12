@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title',  translate('order_by_vip') )
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
<style>
/* Optional: Style for the tooltip */
.ui-tooltip {
max-width: 300px;
padding: 10px;
background-color: #f0f0f0;
border: 1px solid #ccc;
color: #333;
}
body {
margin: 0;
padding: 0;
}
.box {
width: 200px;
height: 200px;
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
overflow: hidden;
}
.box .b {
border-radius: 50%;
border-left: 4px solid;
border-right: 4px solid;
border-top: 4px solid transparent !important;
border-bottom: 4px solid transparent !important;
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
animation: ro 2s infinite;
}
.box .b1 {
border-color: #4A69BD;
width: 120px;
height: 120px;
}
.box .b2 {
border-color: #F6B93B;
width: 100px;
height: 100px;
animation-delay: 0.2s;
}
.box .b3 {
border-color: #2ECC71;
width: 80px;
height: 80px;
animation-delay: 0.4s;
}
.box .b4 {
border-color: #34495E;
width: 60px;
height: 60px;
animation-delay: 0.6s;
}
@keyframes ro {
0% {
transform: translate(-50%, -50%) rotate(0deg);
}

50% {
transform: translate(-50%, -50%) rotate(-180deg);
}

100% {
transform: translate(-50%, -50%) rotate(0deg);
}
}

@keyframes blink {
0% { opacity: 1; }
50% { opacity: 0; }
100% { opacity: 1; }
}
.dateBooking {
animation: blink 1s infinite;
color: red;
}

</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vippooja.png') }}"
        alt="">
        {{ translate('order_by_vip') }}
        <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="VIP-tab" data-toggle="tab" data-target="#VIP" type="button"
            role="tab" aria-controls="VIP" aria-selected="true">VIP Order view
            ({{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 5)->where('status', 0)->count() }})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="instance-tab" data-toggle="tab" data-target="#instance" type="button"
            role="tab" aria-controls="instance" aria-selected="false">Instance Order View (
            {{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 6)->where('status', 0)->count() }})</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="VIP" role="tabpanel" aria-labelledby="home-tab">
            <div class="row mt-20">
                <div class="col-md-12">
                    <div class="card">
                        <div class="px-3 py-4">
                            <div class="row g-2 flex-grow-1">
                                <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="myTable"
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th>{{ translate('Puja_Id') }}</th>
                                            <th>{{ translate('puja_name') }}</th>
                                            <th>{{ translate('puja_date') }}</th>
                                            <th>{{ translate('orders') }}</th>
                                            <th>{{ translate('members') }}</th>
                                            <th>{{ translate('total_amount') }}</th>
                                            <th>{{ translate('pandit_name') }}</th>
                                            <th> {{ translate('puja_status') }}</th>
                                            @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'list') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'detail') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'assign-pandit'))
                                            <th>{{ translate('action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($viporders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>## {{ $order->service_id }}</td>
                                            <td><b> <a href="{{ route('admin.service.vip.view', ['addedBy' => $order['vippoojas']['added_by'], 'id' => $order['vippoojas']['id']]) }}"
                                                class="title-color hover-c1 d-flex align-items-center gap-10"
                                                target="_blank">
                                            {{ Str::limit($order['vippoojas']['name'], 30) }}</a>
                                            </b>
                                            <span class="dateBooking">
                                                {{ $order->booking_date ? date('d ,F , l', strtotime($order->booking_date)) : 'No Date Available' }}
                                            </span>
                                        </td>
                                        <td>{{ date('d M Y', strtotime($order->booking_date)) }} </td>
                                        <td>{{ $order->total_orders }}</td>
                                        <td>
                                            @php
                                            $member_count = 0;
                                            $members = explode('|', $order->members);
                                            foreach ($members as $memb) {
                                            if ($memb != null) {
                                            $member_count += count(json_decode($memb));
                                            }
                                            }
                                            @endphp
                                            @if ($order->members != null)
                                            @php
                                            $members_clean = preg_replace(
                                            ['/\[|\]/', "/'([^']+)'/"],
                                            '',
                                            $order->members,
                                            );
                                            $members_array = explode(',', $members_clean);
                                            @endphp
                                            <span class="tio-user nav-icon"
                                                title="{{ str_replace(',', '<br>', str_replace('"', '', implode(',', $members_array))) }}">
                                            </span>{{ $member_count }}
                                            @else
                                            <span class="badge badge-soft-danger">No Members</span>
                                            @endif
                                        </td>
                                        <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->total_amount), currencyCode: getCurrencyCode()) }}
                                        </td>
                                        <td>
                                            @if ($order->pandit_assign != null)
                                            <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                            @else
                                            <span class="badge badge-soft-danger">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-soft-{{ $order->order_status == 0
                                                ? 'primary'
                                                : ($order->order_status == 1
                                                ? 'success'
                                                : ($order->order_status == 2
                                                ? 'danger'
                                                : ($order->order_status == 3
                                                ? 'warning'
                                                : ($order->order_status == 4
                                                ? 'secondary'
                                                : ($order->order_status == 5
                                                ? 'info'
                                                : ($order->order_status == 6
                                                ? 'warning'
                                                : 'light')))))) }}">
                                                {{ $order->order_status == 0
                                                ? 'Pending'
                                                : ($order->order_status == 1
                                                ? 'Completed'
                                                : ($order->order_status == 2
                                                ? 'Cancel'
                                                : ($order->order_status == 3
                                                ? 'Schedule Time'
                                                : ($order->order_status == 4
                                                ? 'Live Pooja'
                                                : ($order->order_status == 5
                                                ? 'Share Soon'
                                                : ($order->order_status == 6
                                                ? 'Rejected'
                                                : 'Unknown Status')))))) }}
                                            </span>
                                        </td>
                                        @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'list') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'detail') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'assign-pandit'))
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'assign-pandit'))
                                                @if (\Illuminate\Support\Carbon::parse($order['booking_date'])->toDateString() <= \Illuminate\Support\Carbon::now()->addDay()->toDateString())
                                                <!-- <a class="btn btn-outline-warning btn-sm square-btn"
                                                    title="{{ translate('pandit') }}"  href="javascript:void(0);"
                                                    data-id="{{ $order->id }}"   data-serviceid="{{ $order->service_id }}"  data-bookingdate="{{ $order->booking_date }}"
                                                    onclick="pandit_model(this)">
                                                    <img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}" alt="" width="20px" height="20px">
                                                </a> -->
                                                @else
                                                <!-- <a class="btn btn-outline-danger btn-sm square-btn"
                                                    title="{{ translate('pandit') }}" disabled>
                                                    <img src="{{ asset('public/assets/back-end/img/pooja/bellboy.png') }}"
                                                    alt="" width="20px" height="20px">
                                                </a> -->
                                                @endif
                                                @endif
                                                @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'list'))
                                                <a class="btn btn-outline-primary btn-sm square-btn"
                                                    target="_blank" title="All order list"
                                                    href="{{ route('admin.vippooja.order.SingleOrder', [  'service_id' => $order->service_id,'booking_date' => $order->booking_date, 'status' => $order->order_status]) }}">
                                                    <i class="tio-format-points nav-icon"></i>
                                                </a>
                                                @endif
                                                @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'detail'))
                                                <!-- <a class="btn btn-outline-info btn-sm square-btn"
                                                    title="Order Details" target="_blank"
                                                    href="{{ route('admin.vippooja.order.SingleOrderdetails', ['booking_date' => $order->booking_date,'service_id' => $order->service_id, 'status' => 0]) }}">
                                                    <i class="tio-visible nav-icon"></i>
                                                </a> -->
                                                @endif
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{-- {{ $orders->links() }} --}}
                        </div>
                    </div>
                    @if (count($viporders) == 0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                        alt="">
                        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="instance" role="tabpanel" aria-labelledby="home-tab">
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Puja_Id') }}</th>
                                        <th>{{ translate('puja_name') }}</th>
                                        <th>{{ translate('puja_date') }}</th>
                                        <th>{{ translate('orders') }}</th>
                                        <th>{{ translate('members') }}</th>
                                        <th>{{ translate('total_amount') }}</th>
                                        <th>{{ translate('pandit_name') }}</th>
                                        <th>{{ translate('puja_status') }}</th>
                                        @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'list') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'detail') || Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'assign-pandit'))
                                        <th>{{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($instanceorders as $key => $Instorder)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>## {{ $Instorder->service_id }}</td>
                                        <td><b> <a href="{{ route('admin.service.vip.view', ['addedBy' => $Instorder['vippoojas']['added_by'], 'id' => $Instorder['vippoojas']['id']]) }}"
                                            class="title-color hover-c1 d-flex align-items-center gap-10"
                                            target="_blank">
                                        {{ Str::limit($Instorder['vippoojas']['name'], 30) }}</a>
                                    </b></td>
                                    <td>{{ date('d M Y', strtotime($Instorder->booking_date)) }}</td>
                                    <td>{{ $Instorder->total_orders }}</td>
                                    <td>
                                        @php
                                        $member_count = 0;
                                        $members = explode('|', $Instorder->members);
                                        foreach ($members as $memb) {
                                        if ($memb != null) {
                                        $member_count += count(json_decode($memb));
                                        }
                                        }
                                        @endphp
                                        @if ($Instorder->members != null)
                                        @php
                                        $members_clean = preg_replace(
                                        ['/\[|\]/', "/'([^']+)'/"],
                                        '',
                                        $Instorder->members,
                                        );
                                        $members_array = explode(',', $members_clean);
                                        @endphp
                                        <span class="tio-user nav-icon"
                                            title="{{ str_replace(',', '<br>', str_replace('"', '', implode(',', $members_array))) }}">
                                        </span>{{ $member_count }}
                                        @else
                                        <span class="badge badge-soft-danger">No Members</span>
                                        @endif
                                    </td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $Instorder->total_amount), currencyCode: getCurrencyCode()) }}</td>
                                    <td>
                                        @if ($Instorder->pandit_assign != null)
                                        <b>{{ @ucwords($Instorder['pandit']['name']) }}</b>
                                        @else
                                        <span class="badge badge-soft-danger">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-soft-{{ $Instorder->order_status == 0
                                            ? 'primary'
                                            : ($Instorder->order_status == 1
                                            ? 'success'
                                            : ($Instorder->order_status == 2
                                            ? 'danger'
                                            : ($Instorder->order_status == 3
                                            ? 'warning'
                                            : ($Instorder->order_status == 4
                                            ? 'secondary'
                                            : ($Instorder->order_status == 5
                                            ? 'info'
                                            : ($Instorder->order_status == 6
                                            ? 'warning'
                                            : 'light')))))) }}">
                                            {{ $Instorder->order_status == 0
                                            ? 'Pending'
                                            : ($Instorder->order_status == 1
                                            ? 'Completed'
                                            : ($Instorder->order_status == 2
                                            ? 'Cancel'
                                            : ($Instorder->order_status == 3
                                            ? 'Schedule Time'
                                            : ($Instorder->order_status == 4
                                            ? 'Live Pooja'
                                            : ($Instorder->order_status == 5
                                            ? 'Share Soon'
                                            : ($Instorder->order_status == 6
                                            ? 'Rejected'
                                            : 'Unknown Status')))))) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'assign-pandit'))
                                        @if (\Illuminate\Support\Carbon::parse($Instorder['booking_date'])->toDateString() <= \Illuminate\Support\Carbon::now()->addDay()->toDateString())
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-warning btn-sm square-btn"
                                                title="{{ translate('pandit') }}"
                                                href="javascript:void(0);"
                                                data-id="{{$Instorder->id }}"
                                                data-serviceid="{{$Instorder->service_id }}"
                                                data-bookingdate="{{$Instorder->booking_date }}"
                                                onclick="pandit_model(this)">
                                                <img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}" alt="" width="20px" height="20px">
                                            </a>
                                            @else
                                            <div class="spinner-border" title="Available Soon" role="status">
                                                <span class="sr-only"><img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}" alt="" width="20px" height="20px">Loading...</span>
                                            </div>
                                            @endif
                                            @endif

                                            @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'list'))
                                            <a class="btn btn-outline-primary btn-sm square-btn"
                                                target="_blank" title="All order list"
                                                href="{{ route('admin.vippooja.order.SingleOrder', [  'service_id' => $Instorder->service_id,'booking_date' => $Instorder->booking_date, 'status' => $Instorder->order_status]) }}">
                                                <i class="tio-format-points nav-icon"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'detail'))
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="Order Details" target="_blank"
                                                href="{{ route('admin.vippooja.order.instanceOrderdetails', ['booking_date' => $Instorder->booking_date,'service_id' => $Instorder->service_id, 'status' => 0]) }}">
                                                <i class="tio-visible nav-icon"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{-- {{ $orders->links() }} --}}
                    </div>
                </div>
                @if (count($instanceorders) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160"
                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                    alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="box" style="display: none;">
    <div class="b b1"></div>
    <div class="b b2"></div>
    <div class="b b3"></div>
    <div class="b b4"></div>
</div>
</div>
{{-- VIP Model --}}
<div class="modal fade" id="Assgine-the-pandit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Change Pandit</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            
            <div class="">
                
                <div class="" id="in-house">
                    <select name="assign_pandit" id="assign-inhouse-pandit" class="assign-pandit form-control">
                    </select>
                </div>
                
                <form action="{{ route('admin.vippooja.order.assign.allpandit') }}"  method="post" id="assign-pandit-form">
                    @csrf
                    <input type="hidden" name="booking_date" id="booking_date">
                    <input type="hidden" name="service_id" id="service_id">
                    <input type="hidden" name="pandit_id" id="pandit-id-val">
                </form>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            
        </div>
    </div>
</div>
</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
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
let table = new DataTable('#myTable');
</script>
<script>
new DataTable('#example', {
layout: {
topStart: {
buttons: ['print']
}
}
});
</script>
<script>
$('.assign-pandit').on('change', function() {
var panditId = $(this).val();
$('#pandit-id-val').val(panditId);
Swal.fire({
title: 'Are You Sure To Assign Vip  Pandit',
type: 'success',
showCancelButton: true,
confirmButtonColor: '#3085d6',
cancelButtonColor: '#d33',
confirmButtonText: 'Yes',
cancelButtonText: 'Cancel',
reverseButtons: true
}).then((result) => {
if (result.value) {
$('#assign-pandit-form').submit();
}
});
});
function pandit_model(that) {
$('.box').css('display','block');
var panditId=$(that).data('id');
var bookdate=$(that).data('bookingdate');
var serviceId=$(that).data('serviceid');
$('#service_id').val(serviceId);
$('#booking_date').val(bookdate);
var inhouseList = "";
var freelancerList = "";
$.ajax({
type: "get",
url: "{{url('admin/vippooja/order/getpandit')}}"+'/'+serviceId  + '/' + bookdate,
success: function (response) {
if(response.status == 200){
$('#assign-inhouse-pandit').html('');

if(response.inhouse.length > 0){
inhouseList = `<option value="" selected disabled>Select Pandit Ji</option>`;
$.each(response.inhouse, function (key, value) {
if (value.is_pandit_pooja_per_day > value.checkastro) {
inhouseList += `<option value="${value.id}">${value.name}</option>`;
}
});
$('#assign-inhouse-pandit').append(inhouseList);
}else {
$('#assign-inhouse-pandit').append('<option value="" selected disabled>No Pandit Found</option>');
}

$('.box').css('display','none');
$('#Assgine-the-pandit').modal('show');
}
else{
alert('an error occured');
}
}
});
}
</script>

@endpush