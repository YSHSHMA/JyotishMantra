@extends('layouts.back-end.app')

@section('title', translate('withdraw_Info'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                        <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
                        {{translate('withdraw_Request_info')}}
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row my-2">
                        @if($withdrawRequests['status'] == 0)
                        <div class="col-md-2 float-end">
                            <a href="{{ route('admin.donate_management.trustees-withdrawal.rejects',['id'=>$withdrawRequests['id']])}}" class="btn btn-danger">Rejected</a>
                        </div>
                        <div class="col-md-2 float-end">
                            <a href="{{ route('admin.donate_management.trustees-withdrawal.payment-req-approval-admin',['id'=>$withdrawRequests['id'],'type'=>'upi'])}}" class="btn btn-info">Approval UPI</a>
                        </div>
                        <div class="col-md-2 float-end">
                            <a href="{{ route('admin.donate_management.trustees-withdrawal.payment-req-approval-admin',['id'=>$withdrawRequests['id'],'type'=>'bank'])}}" class="btn btn-primary">Approval bank</a>
                        </div>
                        <div class="col-md-2 float-end">
                            <a onclick="$('.manual_forms').toggleClass('d-none')" class="btn btn-primary">Approval Manual</a>
                            <form class="d-none manual_forms" action="{{ route('admin.donate_management.trustees-withdrawal.payment-req-approval-admin',['id'=>$withdrawRequests['id'],'type'=>'manual'])}}" method="get">
                                <input type="text" name="transcation_id" class="form-control my-2" placeholder="Please Enter transcation id" required>
                                <input type="submit" class="btn btn-sm btn-primary">
                            </form>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bolder">Bank Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Holder Name</td>
                                        <td>{{$withdrawRequests['holder_name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Bank Name</td>
                                        <td>{{$withdrawRequests['bank_name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>IFSC code</td>
                                        <td>{{$withdrawRequests['ifsc_code']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Account Number</td>
                                        <td>{{$withdrawRequests['account_number']}}</td>
                                    </tr>
                                    <tr>
                                        <td>UPI Number</td>
                                        <td>{{$withdrawRequests['upi_code']}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($withdrawRequests['ex_id'] == 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bolder">Wallet Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Old Wallet Amount</td>
                                        <td>{{$withdrawRequests['old_wallet_amount']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Request Amount</td>
                                        <td>{{$withdrawRequests['req_amount']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Approval Amount</td>
                                        <td>
                                            @if($withdrawRequests['status'] == 0)
                                            <span class="badge badge-soft-warning badge-pill ml-1">Pending</span>
                                            @elseif($withdrawRequests['status'] == 1)
                                            {{$withdrawRequests['approval_amount']}}
                                            @else
                                            <span class="badge badge-soft-danger badge-pill ml-1">denied</span>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center font-weight-bolder">Trustees Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Trust Name</td>
                                        <td>{{$withdrawRequests['Trust']['trust_name']??""}}</td>
                                    </tr>
                                    <tr>
                                        <td>Request Amount</td>
                                        <td>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequests['req_amount']??0) ), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Approval Amount</td>
                                        <td>
                                            @if($withdrawRequests['status'] == 0)
                                            <span class="badge badge-soft-warning badge-pill ml-1">Pending</span>
                                            @elseif($withdrawRequests['status'] == 1)
                                            {{$withdrawRequests['approval_amount']}}
                                            @else
                                            <span class="badge badge-soft-danger badge-pill ml-1">denied</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($withdrawRequests['status'] == 1)
                                    <tr>
                                        <td>Transaction Id</td>
                                        <td>
                                            <span class="badge badge-soft-success badge-pill ml-1"> {{$withdrawRequests['transcation_id']}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Transaction Method</td>
                                        <td>
                                            <span class="badge badge-soft-success badge-pill ml-1"> {{ $withdrawRequests['payment_method'] }}</span>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="get-status-filter-route" data-action="{{route('vendor.business-settings.withdraw.index')}}"></span>


@endsection
@push('script')
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor/withdraw.js')}}"></script>
@endpush