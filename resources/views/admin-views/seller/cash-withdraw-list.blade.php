@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Cash_withdraw_request'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png') }}" alt="">
                {{ translate('Cash_withdraw_request') }}
            </h2>
        
            <a href="{{route('admin.sellers.withdraw_list') }}"  class="btn btn-info" target="_blank" title="view Online List">
                {{ translate('Online_Withdrawal_request') }}
            </a>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="p-3">
                        <div class="row gy-1 align-items-center justify-content-between">
                            <div class="col-auto">
                                <h5 class="text-capitalize">
                                {{ translate('Cash_withdraw_request_table')}}
                                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $cashwithdrawlist->total() }}</span>
                                </h5>
                            </div>
                            <div class="d-flex col-auto gap-3">
                                <select name="withdraw_status_filter" data-action="{{ url()->current() }}"
                                        class="custom-select min-w-120 withdraw-status-filter">
                                    <option value="all" {{request('status') == 'all' ? 'selected' : ''}}>{{translate('all')}}</option>
                                    <option value="approved" {{request('status') == 'approved' ? 'selected' : ''}}>{{translate('approved')}}</option>
                                    <option value="denied" {{request('status') == 'denied' ? 'selected' : ''}}>{{translate('denied')}}</option>
                                    <option value="pending" {{request('status') == 'pending' ? 'selected' : ''}}>{{translate('pending')}}</option>
                                </select>
                                {{-- <div>
                                    <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                                            data-toggle="dropdown">
                                        <i class="tio-download-to"></i>
                                        {{translate('export')}}
                                        <i class="tio-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.sellers.withdraw-list-export-excel') }}?approved={{request('approved')}}">
                                                <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" alt="">
                                                {{translate('excel')}}
                                            </a>
                                        </li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('amount')}}</th>
                                <th>{{ translate('name') }}</th>
                                <th>{{translate('request_time')}}</th>
                                <th class="text-center">{{translate('status')}}</th>
                                @if (Helpers::modules_permission_check('Vendor', 'Withdraws', 'detail'))
                                <th class="text-center">{{translate('action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cashwithdrawlist as $key => $withdrawRequest)
                                <tr>
                                    <td>{{$cashwithdrawlist->firstItem() + $key }}</td>
                                    <td>{{setCurrencySymbol(currencyConverter($withdrawRequest['amount']), currencyCode: getCurrencyCode(type: 'default'))}}</td>

                                    <td>
                                        @if (isset($withdrawRequest->seller))
                                            <a href="{{route('admin.sellers.view', $withdrawRequest->seller_id)}}" class="title-color hover-c1">{{ $withdrawRequest->seller->f_name . ' ' . $withdrawRequest->seller->l_name }}</a>
                                        @else
                                            <span>{{translate('not_found')}}</span>
                                        @endif
                                    </td>
                                    <td>{{$withdrawRequest->created_at}}</td>
                                    <td class="text-center">
                                        @if($withdrawRequest->status == 0)
                                            <label class="badge badge-soft-primary">{{translate('pending')}}</label>
                                        @elseif($withdrawRequest->status == 1)
                                            <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                        @elseif($withdrawRequest->status == 2)
                                            <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                        @endif
                                    </td>
                                    {{-- @if (Helpers::modules_permission_check('Vendor', 'Withdraws', 'detail')) --}}
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            @if (isset($withdrawRequest->seller))
                                            <a href="{{route('admin.sellers.cash_withdraw_view', ['withdraw_id'=>$withdrawRequest['id'], 'seller_id'=>$withdrawRequest->seller['id']])}}"
                                                class="btn btn-outline-info btn-sm square-btn"
                                                title="{{translate('view')}}">
                                                <i class="tio-invisible"></i>
                                                </a>
                                            @else
                                            <a href="javascript:">
                                                {{translate('action_disabled')}}
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- @endif --}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($cashwithdrawlist) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                        alt="{{translate('image_description')}}">
                                <p class="mb-0">{{translate('no_data_to_show')}}</p>
                            </div>
                    @endif
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            {{ $cashwithdrawlist->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
