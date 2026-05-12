@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('withdraw_Request'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
            {{translate('withdraw')}}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">{{ translate('withdraw_Request_Table')}}
                        <span class="badge badge-soft-dark radius-50 fz-12 ml-1" id="withdraw-requests-count">{{ $withdrawRequests->total() }}</span>
                    </h5>
                    <select name="status" class="custom-select max-w-200 select-status-options" data-action="{{ url()->current() }}">
                        <option value="all">{{translate('all')}}</option>
                        <option value="approved" {{ ((request('approved') == "approved")?'selected':'' ) }}>{{translate('approved')}}</option>
                        <option value="denied" {{ ((request('approved') == "denied")?'selected':'' ) }}>{{translate('denied')}}</option>
                        <option value="pending" {{ ((request('approved') == "pending")?'selected':'' ) }}>{{translate('pending')}}</option>
                    </select>

                </div>
                <div id="status-wise-view">
                    <div class="table-responsive">
                        <table id="datatable"
                            style="text-align: {{Session::get('direction') === 'rtl' ? 'right' : 'left'}};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('vendor_Info')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th>{{translate('status')}}</th>
                                    @if (Helpers::modules_permission_check('Event', 'Withdraw', 'detail'))
                                    <th class="text-center">{{translate('action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($withdrawRequests->count() > 0)
                                @foreach($withdrawRequests as $key=>$withdrawRequest)
                                <tr>
                                    <td>{{$withdrawRequests->firstitem()+$key}}</td>
                                    <td>
                                        <div>
                                            <span>{{ $withdrawRequest['EventOrg']['full_name']??"" }}</span><br>
                                            <span>{{ $withdrawRequest['EventOrg']['email_address']??"" }}</span><br>
                                            <span>{{ $withdrawRequest['EventOrg']['contact_number']??"" }}</span><br>
                                            <span>{{ $withdrawRequest['EventOrg']['organizer_name']??"" }}</span><br>
                                        </div>
                                    </td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['req_amount']??0) ), currencyCode: getCurrencyCode()) }}</td>
                                    <td>
                                        <span>{{date("d M, Y", strtotime($withdrawRequest->created_at))}}</span><br>
                                        <span>{{date("h:i A", strtotime($withdrawRequest->created_at))}}</span><br>
                                        @if($withdrawRequest['status'] != 0)
                                        <hr class="m-0">
                                        <span class="text-{{ (($withdrawRequest['status'] == 1)?'success':'danger')}} font-weight-bolder">{{date("d M, Y", strtotime($withdrawRequest->updated_at))}}</span><br>
                                        <span class="text-{{ (($withdrawRequest['status'] == 1)?'success':'danger')}} font-weight-bolder">{{date("h:i A", strtotime($withdrawRequest->updated_at))}}</span><br>
                                        @endif
                                    </td>
                                    <td>
                                        @if($withdrawRequest['status'] == 0)
                                        <label class="badge badge-soft--primary">{{translate('pending')}}</label>
                                        @elseif($withdrawRequest['status'] == 1)
                                        <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                        @elseif($withdrawRequest['status'] == 2)
                                        <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (Helpers::modules_permission_check('Event', 'Withdraw', 'detail'))
                                        <a href="{{ route('admin.event-managment.event-withdrawal.withdraw-request-view', [$withdrawRequest['id']]) }}" class="btn btn--primary btn-sm">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <td colspan="6" class="text-center">
                                    <img class="mb-3 w-160" src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}" alt="{{translate('image_description')}}">
                                    <p class="mb-0">{{translate('no_data_to_show')}}</p>
                                </td>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {{$withdrawRequests->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor/withdraw.js')}}"></script>
<script>
    $('.select-status-options').change(function(){
        location.href=$(this).data('action')+'?'+'approved'+'='+$(this).val();
    })
</script>
@endpush