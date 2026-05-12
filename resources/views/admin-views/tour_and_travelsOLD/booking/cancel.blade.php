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
<style>
    .chat-container {
        margin: 0 auto;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 500px;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #fff;
        border-bottom: 1px solid #ccc;
    }


    .chat-box {
        padding: 10px;
        flex-grow: 1;
        overflow-y: auto;
        background-color: #f1f1f1;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 10px;
    }

    .chat-input input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
    }

    .chat-input button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 10px;
        margin-left: 10px;
        cursor: pointer;
        width: 44px;
    }

    .chat-input button i {
        font-size: 16px;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        /* border-radius: 10px;
    max-width: 60%; */
        word-wrap: break-word;
    }

    .user-message {
        background-color: #ff9200;
        color: white;
        align-self: flex-end;
        text-align: right;
        border-radius: 8px;
    }

    .admin-message {
        background-color: #f1f1f1;
        color: black;
        align-self: flex-start;
        text-align: left;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <!-- Bootstrap Tab Section -->
    <div class="row">
        <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
            <!-- Use anchor tags for toggling the tabs -->
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('pending_Request')}}</h6>
                </div>
                <span class="order-stats__title">
                    @php
                    echo \App\Models\tourOrder::where('refund_status', 3)->count();
                    @endphp
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('Approve_Request')}}</h6>
                </div>
                <span class="order-stats__title">
                    @php
                    echo \App\Models\tourOrder::where('refund_status', 1)->count();
                    @endphp
                </span>
            </a>
        </div>
    </div>

    <!-- Bootstrap Tab Section with <ul> and <li> -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card w-100">
                <div class="card-body">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item text-capitalize">
                            <a class="nav-link {{ ((request('type') != 'approval')?'active':'')}}" id="pending-request-tab" data-toggle="tab" href="#pending-request">
                                {{ translate('pending') }}
                            </a>
                        </li>
                        <li class="nav-item text-capitalize">
                            <a class="nav-link {{ ((request('type') == 'approval')?'active':'')}}" id="approval-request-tab" data-toggle="tab" href="#approval-request">
                                {{ translate('Approve') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade {{ ((request('type') != 'approval')?'show active':'')}}" id="pending-request">
                            <div class="card-body">
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
                                                    <input type="hidden" name="type" value="pending">
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
                                                    <th class="text-center">{{ translate('final_amount') }}</th>
                                                    <th class="text-center"> {{ translate('TXN_ID') }}</th>
                                                    <th class="text-center"> {{ translate('Re_Amount') }}</th>
                                                    <th class="text-center"> {{ translate('View') }}</th>
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
                                                            <small>{{ date('d M,Y',strtotime($lead['pickup_date']??"")) }} {{ ($lead['pickup_time']??"") }}</small>
                                                            <p data-title="{{($lead['Tour']['tour_name']??'')}}" role='tooltip' data-toggle='tooltip'>{{ Str::limit(($lead['Tour']['tour_name']??""),20) }}</p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='row' style="width: 248px;">
                                                            <div class="col-6">{{ translate('amount') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['amount'] + $lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('coupon_amount') }}</div>
                                                            <div class="col-6"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('gst_amount') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['gst_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('admin_commission') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['admin_commission'])), currencyCode: getCurrencyCode()) }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['final_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                                    <td class="text-center">
                                                        <p data-title="{{ ($lead['transaction_id']) }}" role='tooltip' data-toggle='tooltip'> {{ Str::limit(($lead['transaction_id']),20) }}</p>
                                                    </td>
                                                    <td class="text-center"> {{ ($lead['refund_amount']) }}</td>
                                                    <td class="text-center">
                                                        @if($lead['refund_status'] == 1 || $lead['refund_status'] == 0)
                                                        <a class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}"> <i class="tio-repeat"></i> </a>
                                                        @else
                                                        <a onclick="chat_views(this)" class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}"> <i class="tio-chat"></i> </a>
                                                        <!-- ////////////////////////////// -->

                                                        <div class="modal fade chat-refund-model-{{($lead['id'])}}" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="dateTimeModalLabel">Refund Pay</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="chat-container">
                                                                                    <div class="chat-header">
                                                                                        <i class="tio-money_vs">money_vs</i>&nbsp;
                                                                                        <span>{{translate('Refund_inquiry')}}</span>
                                                                                    </div>
                                                                                    <div class="chat-box" id="form-reload-order-cancel-chat-{{($lead['id'])}}">
                                                                                        @php
                                                                                        $order_id = \App\Models\TourOrder::where('id',$lead['id'])->first();
                                                                                        $get_Chat = \App\Models\TourCancelResonance::where('ticket_id',$order_id['refund_query_id'])->get();
                                                                                        @endphp
                                                                                        @if($get_Chat)
                                                                                        @foreach($get_Chat as $val)
                                                                                        <div class="row">
                                                                                            <div class="col-md-5">
                                                                                                @if($val['type'] == 'user')
                                                                                                <div class="admin-message">
                                                                                                    <div class="chat-message">
                                                                                                        {{$val['msg']}}
                                                                                                    </div>
                                                                                                </div>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-md-2"></div>
                                                                                            <div class="col-md-5">
                                                                                                @if($val['type'] == 'admin')
                                                                                                <div class="user-message">
                                                                                                    <div class="chat-message">
                                                                                                        {{$val['msg']}}
                                                                                                    </div>
                                                                                                </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        @endforeach
                                                                                        @endif
                                                                                    </div>

                                                                                    <div class="chat-input">
                                                                                        <input type="text" id="chatMessage-{{($lead['id'])}}" class="cancel-order-msg-{{($lead['id'])}}" placeholder="Write your message here...">
                                                                                        <input type="hidden" class="cancel-order-id-{{($lead['id'])}}">
                                                                                        <button onclick="sendmessages(`{{($lead['id'])}}`)"><i class="tio-send_outlined">send_outlined</i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- ///////////////////////////// -->
                                                        @endif
                                                        @if($lead['refund_status'] != 1)
                                                        <a onclick="onhistory(this)" class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}" data-refund_amount="{{ ($lead['refund_amount']) }}" data-method="{{ ($lead['payment_method']) }}" data-transaction="{{ ($lead['transaction_id']) }}" data-status="{{ ($lead['refound_id']) }}" data-amount="{{ ($lead['amount'] + $lead['coupon_amount']) }}"> <i class="tio-insurance"></i> </a>
                                                        @endif
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
                        <div class="tab-pane fade  {{ ((request('type') == 'approval')?'show active':'')}}" id="approval-request">
                            <div class="card-body">
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
                                                    <input type="hidden" name="type" value="approval">
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
                                                    <th class="text-center">{{ translate('final_amount') }}</th>
                                                    <th class="text-center"> {{ translate('TXN_ID') }}</th>
                                                    <th class="text-center"> {{ translate('Re_Amount') }}</th>
                                                    <th class="text-center"> {{ translate('View') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($refund_approve as $key => $lead)
                                                <tr>
                                                    <td>{{ $refund_approve->firstItem()+$key }}</td>
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
                                                            <small>{{ date('d M,Y',strtotime($lead['pickup_date']??"")) }} {{ ($lead['pickup_time']??"") }}</small>
                                                            <p data-title="{{($lead['Tour']['tour_name']??'')}}" role='tooltip' data-toggle='tooltip'>{{ Str::limit(($lead['Tour']['tour_name']??""),20) }}</p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='row' style="width: 248px;">
                                                            <div class="col-6">{{ translate('amount') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['amount'] + $lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('coupon_amount') }}</div>
                                                            <div class="col-6"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['coupon_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('gst_amount') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['gst_amount'])), currencyCode: getCurrencyCode()) }}</div>
                                                            <div class="col-6">{{ translate('admin_commission') }}</div>
                                                            <div class="col-6">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['admin_commission'])), currencyCode: getCurrencyCode()) }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($lead['final_amount'])), currencyCode: getCurrencyCode()) }}</td>
                                                    <td class="text-center">
                                                        <p data-title="{{ ($lead['transaction_id']) }}" role='tooltip' data-toggle='tooltip'> {{ Str::limit(($lead['transaction_id']),20) }}</p>
                                                    </td>
                                                    <td class="text-center"> {{ ($lead['refund_amount']) }}</td>
                                                    <td class="text-center">
                                                        @if($lead['refund_status'] == 1 || $lead['refund_status'] == 0)
                                                        <a class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}"> <i class="tio-repeat"></i> </a>
                                                        @else
                                                        <a onclick="chat_views(this)" class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}"> <i class="tio-chat"></i> </a>
                                                        <!-- ////////////////////////////// -->

                                                        <div class="modal fade chat-refund-model-{{($lead['id'])}}" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="dateTimeModalLabel">Refund Pay</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="chat-container">
                                                                                    <div class="chat-header">
                                                                                        <i class="tio-money_vs">money_vs</i>&nbsp;
                                                                                        <span>{{translate('Refund_inquiry')}}</span>
                                                                                    </div>
                                                                                    <div class="chat-box" id="form-reload-order-cancel-chat-{{($lead['id'])}}">
                                                                                        @php
                                                                                        $order_id = \App\Models\TourOrder::where('id',$lead['id'])->first();
                                                                                        $get_Chat = \App\Models\TourCancelResonance::where('ticket_id',$order_id['refund_query_id'])->get();
                                                                                        @endphp
                                                                                        @if($get_Chat)
                                                                                        @foreach($get_Chat as $val)
                                                                                        <div class="row">
                                                                                            <div class="col-md-5">
                                                                                                @if($val['type'] == 'user')
                                                                                                <div class="admin-message">
                                                                                                    <div class="chat-message">
                                                                                                        {{$val['msg']}}
                                                                                                    </div>
                                                                                                </div>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-md-2"></div>
                                                                                            <div class="col-md-5">
                                                                                                @if($val['type'] == 'admin')
                                                                                                <div class="user-message">
                                                                                                    <div class="chat-message">
                                                                                                        {{$val['msg']}}
                                                                                                    </div>
                                                                                                </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        @endforeach
                                                                                        @endif
                                                                                    </div>

                                                                                    <div class="chat-input">
                                                                                        <input type="text" id="chatMessage-{{($lead['id'])}}" class="cancel-order-msg-{{($lead['id'])}}" placeholder="Write your message here...">
                                                                                        <input type="hidden" class="cancel-order-id-{{($lead['id'])}}">
                                                                                        <button onclick="sendmessages(`{{($lead['id'])}}`)"><i class="tio-send_outlined">send_outlined</i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- ///////////////////////////// -->
                                                        @endif
                                                        @if($lead['refund_status'] != 1)
                                                        <a onclick="onhistory(this)" class="btn btn-success btn-sm" data-id="{{ ($lead['id']) }}" data-refund_amount="{{ ($lead['refund_amount']) }}" data-method="{{ ($lead['payment_method']) }}" data-transaction="{{ ($lead['transaction_id']) }}" data-status="{{ ($lead['refound_id']) }}" data-amount="{{ ($lead['amount'] + $lead['coupon_amount']) }}"> <i class="tio-insurance"></i> </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="table-responsive mt-4">
                                    <div class="d-flex justify-content-lg-end">
                                        {{ $refund_approve->links() }}
                                    </div>
                                </div>
                                @if(count($refund_approve)==0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade refund_pay" id="dateTimeModal" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
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
                                <option value="approve" selected>Approve</option>
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
                            <input type="text" class="form-control refund_model_amount" name="refund_amount" require>
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
    // datepicker
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
        $(".refund_model_amount").val($(that).data('refund_amount'));
    }

    function chat_views(that) {
        var id = $(that).data('id');
        $(`.chat-refund-model-${id}`).modal('show');
        reloadInterval = setInterval(function() {
            $(`#form-reload-order-cancel-chat-${id}`).load(location.href + ` #form-reload-order-cancel-chat-${id} > *`);
        }, 2000);
        $(`.chat-refund-model-${id}`).on('hide.bs.modal', function() {
            clearInterval(reloadInterval);
        });
    }

    function sendmessages(id) {
        var order_id = id;
        var msg = $(`.cancel-order-msg-${id}`).val();
        $(`.cancel-order-msg-${id}`).val('');
        $.ajax({
            url: "{{ route('tour.cancel-order-resonance')}}",
            data: {
                type: "admin",
                msg,
                order_id,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                $(`#form-reload-order-cancel-chat-${id}`).load(location.href + ` #form-reload-order-cancel-chat-${id} > *`);
            }
        });
    }
</script>
@endpush