@extends('layouts.back-end.app')

@section('title', translate('Tour_details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/refund_transaction.png')}}" alt="">
            {{translate('Tour_details')}}
        </h2>
    </div>
    <div class="refund-details-card--2 p-4">
        <div class="row gy-2">
            <div class="col-lg-4">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <h4 class="mb-3">{{translate('Payment_summary')}}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{translate('transaction_id')}} </span> <span>:</span> <span class="right">{{$getData['transaction_id']}}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{translate('booking_date')}}</span>
                                <span>:</span>
                                <span class="right">{{date('d M Y, h:s:A',strtotime($getData['created_at']))}}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{translate('payment_method')}} </span> <span>:</span> <span class="right">{{str_replace('_',' ',$getData['payment_method'])}}</span>
                            </li>
                        </ul>
                        @if($getData['refund_status'] != 0)
                        <h4 class="mb-1 mt-3">{{translate('Refund_summary')}}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{translate('refund_id')}} </span> <span>:</span> <span class="right">{{$getData['refound_id']}}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{translate('refund_date')}}</span>
                                <span>:</span>
                                <span class="right">
                                    @if($getData['refund_date'])
                                    {{date('d M Y, h:s:A',strtotime($getData['refund_date']))}}
                                    @endif
                                </span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{translate('refund_status')}} </span> <span>:</span>
                                <span class="right">
                                    @if($getData['refund_status'] == 1)
                                    {{translate('success')}}
                                    @elseif($getData['refund_status'] == 2)
                                    {{translate('progressed')}}
                                    @elseif($getData['refund_status'] == 3)
                                    {{translate('failed')}}
                                    @else
                                    {{translate('pending')}}
                                    @endif

                                </span>
                            </li>
                        </ul>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <div class="gap-3 mb-4 d-flex justify-content-between flex-wrap align-items-center">
                            <h4 class="">{{translate('Tour_details')}}</h4>

                        </div>
                        <div class="refund-details">
                            <div class="img">
                                <div class="onerror-image border rounded">
                                    <img src="{{getValidImage(path:  'storage/app/public/tour_and_travels/tour_visit/'.($getData['Tour']['tour_image']??''),type: 'backend-product')}}" alt="">
                                </div>
                            </div>
                            <div class="--content flex-grow-1">
                                <h4>
                                    <a href="{{route('admin.tour_visits.overview',[$getData['Tour']['id']] )}}">
                                        {{$getData['Tour']['tour_name']}}
                                    </a>
                                </h4>


                            </div>
                            <ul class="dm-info p-0 m-0 w-l-115">
                                <li>
                                    <span class="left">{{translate('people')}}</span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{$getData['qty']}}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{translate('total_price')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['amount'] + $getData['coupon_amount'])), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{translate('coupon_discount')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['coupon_amount']??0)), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>

                                <li>
                                    <span class="left">{{translate('total_tax')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['gst_amount']??0)), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{translate('admin_commission')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['admin_commission']??0)), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{translate('subtotal')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['final_amount']??0)), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>
                                @if($getData['refund_status'] != 0)
                                <li>
                                    <span class="left">{{translate('refundable_amount')}} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($getData['refund_amount']??0)), currencyCode: getCurrencyCode())}}
                                        </strong>
                                    </span>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h4 class="d-flex gap-2">
                                    <img src="http://localhost:8000/assets/back-end/img/vendor-information.png" alt="">  Customer information
                                </h4>
                            </div>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70" src="{{ getValidImage(path: 'storage/app/public/profile/'. $getData['userData']['image'], type: 'backend-product')  }}" alt="Image">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color"><strong>{{ $getData['userData']['name']}}</strong></span>
                                    <span class="title-color break-all"><strong>{{ $getData['userData']['phone']}}</strong></span>
                                    @if($getData['userData']['phone'] != $getData['userData']['email'])
                                    <span class="title-color break-all">{{ $getData['userData']['email']}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{translate('refund_reason_by_customer')}}</h4>
                        <div class="row">
                            <div class="col-12 table-responsive datatable-custom">
                                <table class="table table-hover text-center table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{translate('SL')}}</th>
                                            <th>{{translate('user_message')}}</th>
                                            <th>{{translate('admin_message')}}</th>
                                            <th>{{translate('date')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                                <div class="text-center p-4">
                                    <img class="mb-3 w-160" src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                        alt="{{translate('image_description')}}">
                                    <p class="mb-0">{{ translate('no_data_to_show')}}</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{translate('cab_info')}}</h4>
                        <div class="key-val-list d-flex flex-column gap-2 min-width--60px">
                            <div class="key-val-list-item">
                                    <form action="{{ route('admin.tour-visits-booking.assigned-cab')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class='col-md-12'>
                                                <label class="text-capitalize">{{translate('select_cab_company')}}</label>
                                                <input type="hidden" name='id' value='{{ $getData["id"]}}'>
                                                <select name="cab_id" class="form-control">
                                                    <option value="" selected disabled>Select Cab Company</option>
                                                    @if(!empty($company_list))
                                                    @foreach($company_list as $va)
                                                    <option value="{{ $va['id'] }}">{{ $va['company_name'] }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <button type="submit" class="btn btn-success float-end">Submit</button>                                                
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <hr>                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @if($getData['company'])
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{translate('company_name')}}</span>:
                                <span>{{$getData['company']['company_name']}}</span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{translate('email_address')}}</span>:
                                <span>
                                    <a class="text-dark"
                                        href="mailto:{{ $getData['company']['email'] }}">{{$getData['company']['email'] }}
                                    </a>
                                </span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{translate('phone_number')}} </span>:
                                <span>
                                    <a class="text-dark"
                                        href="tel:{{ $getData['company']['phone_no'] }}">{{$getData['company']['phone_no'] }}
                                    </a>
                                </span>
                            </div>                            
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('script')
@endpush