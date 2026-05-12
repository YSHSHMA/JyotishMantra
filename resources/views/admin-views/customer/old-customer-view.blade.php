@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('customer_Details'))

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <div class="mb-3">
                        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/customer.png')}}" alt="">
                            {{translate('customer_details')}}
                        </h2>
                    </div>
                    <div class="d-sm-flex align-items-sm-center">
                        <h3 class="page-header-title">{{translate('customer_ID')}} #{{$customer['id']}}</h3>
                        <span class="{{Session::get('direction') === "rtl" ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3'}}">
                        <i class="tio-date-range">
                        </i> {{translate('joined_At').':'.date('d M Y H:i:s',strtotime($customer['created_at']))}}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <div class="p-3">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue"
                                               class="form-control"
                                               placeholder="{{translate('search_orders')}}" aria-label="Search orders"
                                               value="{{ request('searchValue') }}"
                                               required>
                                        <button type="submit" class="btn btn--primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{translate('sl')}}</th>
                                <th>{{translate('order_ID')}}</th>
                                <th>{{translate('total')}}</th>
                                <th>{{translate('order_Status')}}</th>
                                @if (Helpers::modules_permission_check('Customers', 'Customer List', 'order-detail') || Helpers::modules_permission_check('Customers', 'Customer List', 'order-download'))
                                <th class="text-center">{{translate('action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstItem()+$key}}</td>
                                    <td>
                                        <a href="{{route('admin.orders.details',['id'=>$order['id']])}}"
                                           class="title-color hover-c1">{{$order['id']}}</a>
                                    </td>
                                    <td> {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}</td>
                                    <td>
                                        @if($order['order_status']=='pending')
                                            <span class="badge badge-soft-info fz-12">
                                                    {{translate($order['order_status'])}}
                                                </span>

                                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                            <span class="badge badge-soft-warning fz-12">
                                                    {{str_replace('_',' ',$order['order_status'] == 'processing' ? translate('packaging'):translate($order['order_status']))}}
                                                </span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge badge-soft-success fz-12">
                                                    {{translate($order['order_status'])}}
                                                </span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-soft-danger fz-12">
                                                    {{translate('failed_to_deliver')}}
                                                </span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge badge-soft-success fz-12">
                                                    {{translate($order['order_status'])}}
                                                </span>
                                        @else
                                            <span class="badge badge-soft-danger fz-12">
                                                    {{translate($order['order_status'])}}
                                                </span>
                                        @endif
                                    </td>
                                    @if (Helpers::modules_permission_check('Customers', 'Customer List', 'order-detail') || Helpers::modules_permission_check('Customers', 'Customer List', 'order-download'))
                                    <td>
                                        <div class="d-flex justify-content-center gap-10">
                                            @if (Helpers::modules_permission_check('Customers', 'Customer List', 'order-detail'))
                                            <a class="btn btn-outline--primary btn-sm edit square-btn"
                                               title="{{translate('view')}}"
                                               href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                    class="tio-invisible"></i> </a>
                                                    @endif
                                                    @if (Helpers::modules_permission_check('Customers', 'Customer List', 'order-download'))
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                               title="{{translate('invoice')}}"
                                               target="_blank"
                                               href="{{route('admin.orders.generate-invoice',[$order['id']])}}"><i
                                                    class="tio-download"></i> </a>
                                                    @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($orders)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160"
                                     src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                                     alt="{{translate('image_description')}}">
                                <p class="mb-0">{{ translate('no_data_to_show')}}</p>
                            </div>
                        @endif
                        <div class="card-footer">
                            {!! $orders->links() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    @if($customer)
                        <div class="card-body">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png')}}" alt="">
                                {{translate('customer')}}
                            </h4>

                            <div class="media">
                                <div class="mr-3">
                                    <img class="avatar rounded-circle avatar-70" src="{{ getValidImage(path: 'storage/app/public/profile/'. $customer['image'] , type: 'backend-profile') }}"
                                        alt="{{translate('image')}}">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color hover-c1"><strong>{{$customer['f_name'].' '.$customer['l_name']}}</strong></span>
                                    <span class="title-color">
                                        <strong>{{count($customer['orders'])}} </strong>{{translate('orders')}}
                                    </span>
                                    <span class="title-color"><strong>{{$customer['phone']}}</strong></span>
                                    <span class="title-color">{{$customer['email']}}</span>
                                </div>
                                <div class="media-body text-right">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
