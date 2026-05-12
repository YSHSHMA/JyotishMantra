@extends('layouts.back-end.app-trustees')

@section('title', translate('withdraw_Request'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@php
use App\Utils\Helpers;
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
                    <div class="row">
                        <div class="col-6">
                            @if(Helpers::Employee_modules_permission('Withdrawal Management', 'List', 'Add'))
                            <a class="btn btn-primary" onclick="withdrowal_models(`{{ $relationEmployees }}`)">Withdrawal</a>
                            @endif
                        </div>
                        <div class="col-6">
                            <select name="status" class="custom-select max-w-200 status-filter">
                                <option value="all">{{translate('all')}}</option>
                                <option value="approved">{{translate('approved')}}</option>
                                <option value="denied">{{translate('denied')}}</option>
                                <option value="pending">{{translate('pending')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="status-wise-view">
                    <div class="table-responsive">
                        <table id="datatable"
                            style="text-align: {{Session::get('direction') === 'rtl' ? 'right' : 'left'}};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{translate('Req_amount')}}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th>{{translate('status')}}</th>
                                    @if(Helpers::Employee_modules_permission('Withdrawal Management', 'List', 'Details'))
                                    <th class="text-center">{{translate('action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($withdrawRequests->count() > 0)
                                @foreach($withdrawRequests as $key=>$withdrawRequest)
                                <tr>
                                    <td>{{$withdrawRequests->firstitem()+$key}}</td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['old_wallet_amount']??0) ), currencyCode: getCurrencyCode()) }}</td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['req_amount']??0) ), currencyCode: getCurrencyCode()) }}</td>
                                    <td>{{date("d M,Y h:i A", strtotime($withdrawRequest->created_at))}}</td>
                                    <td>
                                        @if($withdrawRequest['status'] == 0)
                                        <label class="badge badge-soft--primary">{{translate('pending')}}</label>
                                        @elseif($withdrawRequest['status'] == 1)
                                        <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                        @elseif($withdrawRequest['status'] == 2)
                                        <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                        @endif
                                    </td>
                                    @if(Helpers::Employee_modules_permission('Withdrawal Management', 'List', 'Details'))
                                    <td class="text-center">
                                        <a href="{{ route('trustees-vendor.withdraw.withdraw-request-view', [$withdrawRequest['id']]) }}" class="btn btn--primary btn-sm">
                                            <i class="tio-invisible"></i>
                                        </a>
                                    </td>
                                    @endif
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

<div class="modal fade modal-center withdrowal-models" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="tio-clear" aria-hidden="true"></i></button>
                <h4 class="modal-title">Withdrawal Request Amount</h4>
                <form action="{{ route('trustees-vendor.withdraw.add-request-admin-send')}}" method="post">
                    @csrf
                    <div class="row mt-2">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bolder">switch Bank Account</label>
                            <select class="form-control bank-select-dropdown" onchange="onBankChange(this)">
                                <option value="">Select a bank account</option>
                            </select>
                        </div>
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
                            <input type="text" name="req_amount" min="" max="" class="form-control req_amount_place_show" placeholder="" onblur="validateMinMax(this)">
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
<script>
    function withdrowal_models(id) {
        $.ajax({
            url: "{{ route('trustees-vendor.withdraw.get-vendor-data')}}",
            data: {
                id,
                _token: '{{ csrf_token() }}'
            },

            dataType: "json",
            type: "post",
            success: function(data) {
                var status = data.success;
                if (status == 1) {
                    toastr.success(data.message);
                    var bank = data.bank_info;
                    $('.withdrowal-models').modal('show');
                    populateBankDropdown(data.banklistdata, bank);
                    setBankInfo(bank);
                    $(".holder_name_val").val(bank.bank_holder_name);
                    $(".bank_name_val").val(bank.bank_name);
                    $(".IFSC_code_val").val(bank.ifsc_code);
                    $(".account_number_val").val(bank.account_number);
                    $(".withdrawal_total_reqs").text((data.amount).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    }));
                    $(".withdrawal_total_reqs").data('amount', data.amount);
                    $(".withdrawal_total_amounts").val(data.amount);
                    $(".req_amount_place_show").attr("placeholder", `Total Request Amount : ${(data.amount).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
                    $(".req_amount_place_show").attr("min", 10);
                    $(".req_amount_place_show").attr("max", data.amount);
                } else {
                    toastr.error(data.message);
                }
            }
        })
    }
</script>
<script>
    function validateMinMax(input) {
        input.value = input.value.replace(/[^0-9.]/g, '');
        let parts = input.value.split('.');
        if (parts.length > 2) {
            input.value = parts[0] + '.' + parts.slice(1).join('');
        }
        let min = parseFloat(input.getAttribute("min")) || 0;
        let max = parseFloat(input.getAttribute("max")) || Infinity;
        let floatValue = parseFloat(input.value) || 0;
        if (floatValue < min) {
            input.value = min;
            $(".min-max-error-show").text(`Minimum withdrawal amount Rs ${(min).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`).fadeIn(200).delay(1000).fadeOut(2000);
        } else if (floatValue > max) {
            input.value = max;
            $(".min-max-error-show").text(`You can withdraw only Rs ${(max).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`).fadeIn(200).delay(1000).fadeOut(2000); // Hide slowly over 2 seconds

        }
    }

    function populateBankDropdown(bankList, currentBank) {
        var dropdown = $(".bank-select-dropdown");
        dropdown.empty();
        bankList.forEach(function(bank, index) {
            var displayText = bank.bank_holder_name + " - " + bank.bank_name + " (" + bank.account_number + ")";
            var isSelected = (bank.account_number === currentBank.account_number);
            dropdown.append($('<option>', {
                value: index,
                text: displayText,
                selected: isSelected,
                'data-bank-info': JSON.stringify(bank)
            }));
        });
    }

    function setBankInfo(bank) {
        $(".holder_name_val").val(bank.bank_holder_name);
        $(".bank_name_val").val(bank.bank_name);
        $(".IFSC_code_val").val(bank.ifsc_code);
        $(".account_number_val").val(bank.account_number);
    }

    function onBankChange(selectElement) {
        var selectedOption = $(selectElement).find('option:selected');
        var bankInfo = (selectedOption.data('bank-info'));
        setBankInfo(bankInfo);
    }
</script>
@endpush