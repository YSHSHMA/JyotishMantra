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

                    <select name="status" class="custom-select max-w-200 status-filter">
                        <option value="all">{{translate('all')}}</option>
                        <option value="approved">{{translate('approved')}}</option>
                        <option value="denied">{{translate('denied')}}</option>
                        <option value="pending">{{translate('pending')}}</option>
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
                                    <th>{{translate('tour_Info')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th>{{translate('status')}}</th>
                                    @if (Helpers::modules_permission_check('Tour', 'Tour Withdraw', 'detail'))
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
                                            <span>{{ $withdrawRequest['Tour']['person_name']??"" }}</span><br>
                                            <span>{{ $withdrawRequest['Tour']['person_email']??"" }}</span><br>
                                            <span>{{ $withdrawRequest['Tour']['person_phone']??"" }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="font-weight-bolder">{{ $withdrawRequest['TourVisit']['order_id']??"" }}</span><br>
                                            <span class="tooltip-right" title="{{ $withdrawRequest['TourVisit']['Tour']['tour_name'] ?? '' }}"
                                                data-toggle="tooltip" data-placement="right">
                                                {{ Str::limit(($withdrawRequest['TourVisit']['Tour']['tour_name'] ?? ''),25) }}
                                            </span><br>
                                            <span class="font-weight-bold">Total Amount : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['TourVisit']['amount']??0) ), currencyCode: getCurrencyCode()) }}</span><br>
                                        </div>
                                    </td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['req_amount']??0) ), currencyCode: getCurrencyCode()) }}</td>
                                    <td>{{date("d M, Y h:i A", strtotime($withdrawRequest->created_at))}}</td>
                                    <td>
                                        @if($withdrawRequest['status'] == 0)
                                        <label class="badge badge-soft--primary">{{translate('pending')}}</label>
                                        @elseif($withdrawRequest['status'] == 1)
                                        <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                        @elseif($withdrawRequest['status'] == 2)
                                        <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                        @endif
                                    </td>
                                    @if (Helpers::modules_permission_check('Tour', 'Tour Withdraw', 'detail'))
                                    <td class="text-center">
                                        <a href="{{ route('admin.tour_withdrawal.withdraw-request-view', [$withdrawRequest['id']]) }}" class="btn btn--primary btn-sm">
                                            <i class="tio-invisible"></i>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <td colspan="5" class="text-center">
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
<span id="get-status-filter-route" data-action="{{-- route('vendor.business-settings.withdraw.index') --}}"></span>

<div class="modal fade modal-center withdrowal-models" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="tio-clear" aria-hidden="true"></i></button>
                <h4 class="modal-title">Withdrawal Request Amount</h4>
                <form action="{{-- route('tour.withdraw.add-request-admin-send') --}}" method="post">
                    @csrf
                    <div class="row mt-2">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bolder">Holder Name</label>
                            <input type="text" name="holder_name" class="form-control holder_name_val">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bolder">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control bank_name_val">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bolder">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control IFSC_code_val">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bolder">Account Number</label>
                            <input type="text" name="account_number" class="form-control account_number_val">
                            <input type="hidden" name="wallet_amount" class="form-control withdrawal_total_amounts">

                        </div>
                        <div class="col-12 text-center">
                            <hr>
                            <label class="font-weight-bolder">Or</label>
                            <hr>
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="font-weight-bolder">URI</label>
                            <input type="text" name="upi_code" class="form-control" placeholder="abc@okhdfc">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bolder w-100">Withdrawal Amount</label>
                            <span class="font-weight-bolder withdrawal_total_reqs h3" data-amount="0"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" name="req_amount" min="" max="" class="form-control req_amount_place_show" placeholder="" onkeyup="validateMinMax(this)">
                            <span class="text-danger min-max-error-show"></span>
                        </div>
                        <div class="col-md-12 form-group text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor/withdraw.js')}}"></script>
@endpush