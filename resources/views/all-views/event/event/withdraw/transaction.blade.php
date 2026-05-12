@extends('layouts.back-end.app-event')

@section('title', translate('transaction'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
            {{translate('transaction')}}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">{{ translate('transaction_Table')}}
                        <span class="badge badge-soft-dark radius-50 fz-12 ml-1" id="withdraw-requests-count">{{ $transactionhistory->total() }}</span>
                    </h5>
                  
                </div>
                <div id="status-wise-view">
                    <div class="table-responsive">
                        <table id="datatable"
                            style="text-align: {{Session::get('direction') === 'rtl' ? 'right' : 'left'}};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('Event_info')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th>{{translate('status')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($transactionhistory->count() > 0)
                                @foreach($transactionhistory as $val)
                                <tr>
                                    <td>{{ $loop->index + 1}}</td>
                                    <td>
                                        <span>{{ ($val['EventData']['event_name']??"") }}</span><br>
                                        <span>{{ ($val['EventData']['start_to_end_date']??"") }}</span><br>
                                    </td>
                                    <td>
                                        <span class="font-weight-bolder">{{ ($val['amount']??"") }}</span><br>
                                        <span>{{ ($val['transaction_id']??"") }}</span><br>
                                    </td>
                                    <td>{{ date('d M,Y h:i A',strtotime($val['created_at'])) }}</td>
                                    <td>{{ (($val['status'] == 1)?"Success" :"Pending") }}</td>
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
                            {{ $transactionhistory->links() }}
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
</script>
@endpush