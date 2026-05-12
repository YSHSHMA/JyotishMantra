@php use Illuminate\Support\Str; @endphp
@extends('layouts.back-end.app')
@section('title',translate('refund_transactions'))

@section('content')
    <div class="content container-fluid ">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/order_report.png')}}" alt="">
                {{ translate('transaction_report')}}
            </h2>
        </div>
        @include('admin-views.report.transaction-report-inline-menu')

        <div class="card">
            <div class="card-header border-0 px-3 py-4">
                <div class="w-100 d-flex flex-wrap gap-3 align-items-center">
                    <h4 class="mb-0 mr-auto">
                        {{ translate('total_transaction')}}
                        <span class="badge badge-soft-dark radius-50 fz-14">{{$refundTransactions->total()}}</span>
                    </h4>
                    <form action="{{ url()->current() }}" method="GET" class="mb-0">
                        <div class="input-group input-group-merge input-group-custom">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                   placeholder="{{ translate('search_by_orders_id_or_refund_id')}}"
                                   aria-label="Search orders"
                                   value="{{ $searchValue }}">
                            <button type="submit" class="btn btn--primary">{{ translate('search')}}</button>
                        </div>
                    </form>
                    <form action="#" id="form-data" method="GET">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <select class="form-control __form-control w-auto" name="payment_method"
                                    id="payment_method">
                                <option
                                    value="all" {{ $paymentMethod=='all' ? 'selected': '' }}>{{translate('all')}}</option>
                                <option
                                    value="cash" {{ $paymentMethod=='cash' ? 'selected': '' }}>{{translate('cash')}}</option>
                                <option
                                    value="digitally_paid" {{ $paymentMethod=='digitally_paid' ? 'selected': '' }}>{{translate('digitally_paid')}}</option>
                                <option
                                    value="customer_wallet" {{ $paymentMethod=='customer_wallet' ? 'selected': '' }}>{{translate('customer_wallet')}}</option>
                            </select>
                            <button type="submit" class="btn btn--primary px-4 min-w-120 __h-45px" id="formUrlChange"
                                    data-action="{{ url()->current() }}">
                                {{translate('filter')}}
                            </button>
                            <div>
                                <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                                        data-toggle="dropdown">
                                    <i class="tio-download-to"></i>
                                    {{translate('export')}}
                                    <i class="tio-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.report.transaction.refund-transaction-export', ['payment_method'=>$paymentMethod, 'search'=>$searchValue]) }}">
                                            <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}"
                                                 alt="">
                                            {{translate('excel')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatable"
                       class="text-start table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 __table-refund">
                    <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('product')}}</th>
                        <th>{{translate('refund_id')}}</th>
                        <th>{{translate('order_id')}}</th>
                        <th>{{translate('shop_name')}}</th>
                        <th>{{translate('payment_method') }}</th>
                        <th>{{translate('payment_status')}}</th>
                        <th>{{translate('paid_by')}}</th>
                        {{-- <th>{{translate('amount')}}</th> --}}
                        <th>{{translate('Seller_Total_Amount_Collect_from_customer_with_tax')}}</th>
                        <th>{{translate('Refund_Amount_Request_without_tax')}}</th>
                        <th>{{translate('Tax_amount__paid_to_customer_from_admin_side')}}</th>
                        <th>{{translate('Finally_refund_amount')}}</th>
                        <th>{{translate('admin_collect_tax')}}</th>
                        <th class="text-center">{{translate('transaction_type')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                   {{-- @php
                   dd($refundTransactions);
                   @endphp  --}}
                       
                    @foreach ($refundTransactions as $key=>$refund_transaction)
                        <tr class="text-capitalize">
                            <td>
                                {{$refundTransactions->firstItem()+$key}}
                            </td>
                            <td>
                                @if($refund_transaction->orderDetails->product)
                                    <a href="{{route('admin.products.view',['addedBy'=>$refund_transaction->orderDetails->product->added_by,'id'=>$refund_transaction->orderDetails->product->id])}}"
                                       class="media align-items-center gap-2">
                                        <img
                                            src="{{ getValidImage(path:productImagePath(type: 'thumbnail').'/'.$refund_transaction->orderDetails->product->thumbnail,type: 'backend-product')}}"
                                            class="avatar border" alt="">
                                        <span class="media-body title-color hover-c1">
                                            {{ isset($refund_transaction->orderDetails->product->name) ? Str::limit($refund_transaction->orderDetails->product->name, 20) : '' }}
                                        </span>
                                    </a>
                                @else
                                    <span>{{translate('not_found')}}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($refund_transaction->refund_id)
                                    <a href="{{route('admin.refund-section.refund.details',['id'=>$refund_transaction['refund_id']])}}"
                                       class="title-color hover-c1">
                                        {{$refund_transaction->refund_id}}
                                    </a>
                                @else
                                    <span>{{translate('not_found')}}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('admin.orders.details',['id'=>$refund_transaction->order_id])}}"
                                   class="title-color hover-c1">
                                    {{$refund_transaction->order_id}}
                                </a>
                            </td>
                            <td>
                                @if($refund_transaction->order->seller_is == 'seller' && $refund_transaction->order->seller)
                                    {{ $refund_transaction->order->seller->shop->name }}
                                @else
                                    {{translate('inhouse')}}
                                @endif
                            </td>

                            <td>
                                {{translate(str_replace('_',' ',$refund_transaction->payment_method))}}
                            </td>
                            <td>
                                {{translate(str_replace('_',' ',$refund_transaction->payment_status))}}
                            </td>
                            <td>
                                {{translate($refund_transaction->paid_by)}}
                            </td>
                            {{-- <td>
                                {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $refund_transaction->amount), currencyCode: getCurrencyCode())}}
                            </td> --}}
                            <td>{{ $refund_transaction->order->order_amount }}</td>
                            @php
                                $refundRequest = App\Models\RefundRequest::where('order_details_id',$refund_transaction->order_details_id)->first();
                                $refundTax = $refundRequest ? $refundRequest->admin_tax_given : 0;
                                $refundAmount = $refundRequest ? $refundRequest->amount : 0;
                            @endphp
                            <td>₹ {{ $refundAmount-$refundTax }}</td>
                            <td>₹ {{ $refundTax }}</td>
                            <td>₹ {{ $refundAmount }}</td>
                            <td>₹ {{ $refundTax>0?'-'.$refundTax:$refundTax}} </td>
                            <td class="text-center">
                                {{ $refund_transaction->transaction_type == 'Refund' ? translate('refunded') : str_replace('_',' ',$refund_transaction->transaction_type)}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($refundTransactions)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160" src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                             alt="Image Description">
                        <p class="mb-0">{{ translate('no_data_to_show')}}</p>
                    </div>
                @endif
            </div>

            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {{$refundTransactions->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection