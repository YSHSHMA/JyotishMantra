@extends('layouts.back-end.app')

@section('title', 'PRASAAD PENDING ORDER')
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/vendor/datatables/dataTables.bootstrap4.min.css') }}"
        rel="stylesheet">
@endpush
@php
    use Carbon\Carbon;
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
@endphp
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('Puja_prasaad_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
        </div>
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">

                    <div class="col-lg-12">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-info">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('pooja_status', 1)->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                        </div>
                                        <div>
                                            <img width="40" class="mb-2"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-success">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('order_status', 'confirmed')->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">CONFIREMD ORDER</div>
                                        </div>
                                        <div>
                                            <img width="40"
                                                class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-primary">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('order_status', 'pickup' ?? 'out_for_pickup')->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">PICKUP ORDER</div>
                                        </div>
                                        <div>
                                            <img width="40"
                                                class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">

                                            <h3 class="mb-1 fz-24 text-info">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('order_status', 'out_for_delivered')->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">OUTOF DELIVERED ORDER</div>
                                        </div>
                                        <div>
                                            <img width="70"
                                                class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/reject.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">

                                            <h3 class="mb-1 fz-24 text-success">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('order_status', 'delivered')->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">DELIVERED ORDER</div>
                                        </div>
                                        <div>
                                            <img width="70"
                                                class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/reject.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">

                                            <h3 class="mb-1 fz-24 text-danger">
                                                {{ \App\Models\Prashad_deliverys::where('status', 1)->where('order_status', 'cancel')->count() }}
                                            </h3>
                                            <div class="text-capitalize mb-0">CANCEL ORDER</div>
                                        </div>
                                        <div>
                                            <img width="70"
                                                class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/reject.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="card">
            <div class="card-body">
                <form action="{{url()->current()}}" id="form-data" method="GET">
                    <div class="row gx-2">
                        <div class="col-12">
                            <h4 class="mb-3 text-capitalize">{{translate('filter_order')}}</h4>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color" for="customer_filter">{{translate('customer')}}</label>
                                <select  id="" name="customer_filter" class="js-data-example-ajax form-control form-ellipsis">
                                    <option value="all">{{translate('all_customer')}}</option>
                                    <option value="guest">Guest</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user['id']}}">{{$user['f_name'].' '.$user['l_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <label class="title-color" for="date_type_filter">{{translate('date_type')}}</label>
                            <div class="form-group">
                                <select class="form-control __form-control"s name="date_type_filter" id="daste_type">
                                    <option value="" selected disabled>{{stranslate('select_Date_Type')}}s</option>
                                    <option value="this_year">{{translate('this_Year')}}</soption>
                       s             <option value="this_month">{{translate('this_Month')}}</option>
                                    <option value="this_week">{{translate('this_Week')}}</option>
                                    <option value="custom_date">{{translate('custom_Date')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="from-to-div" style="display: none;">
                            <div class="row">
                                <div class="col-sm-6 col-lg-4 col-xl-3" id="from_div">
                                    <label class="title-color" for="customer">{{translate('start_date')}}</label>
                                    <div class="form-group">
                                        <input type="date" name="from" id="from_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3" id="to_div">
                                    <label class="title-color" for="customer">{{translate('end_date')}}</label>
                                    <div class="form-group">
                                        <input type="date" name="to" id="to_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div classs="d-flex gap-3 justify-content-send">
                                <button type="submit" class="btn btn--primary px-5" id="formUrlChange">
                                    {{translate('show_data')}}
                                </button>
                            </div>
                        </div>
                    </divs>
                </form>
     s       </div>
        </div> --}}

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            {{-- <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}"
                                            aria-label="{{ translate('search_by_name') }}"
                                            value="{{ request('searchValue') }}" required>
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div> --}}
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                                                        <i class="tio-download-to"></i>
                                                                        {{ translate('export') }}
                                                                        <i class="tio-chevron-down"></i>
                                                                    </button> -->
                                {{-- <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.calculator.export', ['searchValue' => request('searchValue')]) }}">
                                            <img width="14"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                                                alt="">
                                            {{ translate('excel') }}
                                        </a>
                                    </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Order_id') }}</th>
                                        <th>{{ translate('Pooja Name') }}</th>
                                        <th>{{ translate('Order_completed_date') }}</th>
                                        <th>{{ translate('Customer_Details') }}</th>
                                        <th>{{ translate('Status') }}</th>
                                        <th class="text-center"> {{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->order_id }}</td>
                                            <td>
                                                @if ($order->type == 'pooja')
                                                    <a
                                                        href="#"class="order-link">{{ $order['services']['name'] }}</a>
                                                    @if ($order->services->pooja_type == 1)
                                                        <span class="badge badge-danger">S</span>
                                                    @else
                                                        <span class="badge badge-warning">N</span>
                                                    @endif
                                                @elseif($order->type == 'vip' || $order->type == 'anushthan')
                                                    <a href="#"
                                                        class="order-link">{{ $order['vippoojas']['name'] }}</a>
                                                    @if ($order->vippoojas->is_anushthan == 1)
                                                        <span class="badge badge-danger">A</span>
                                                    @else
                                                        <span class="badge badge-warning">V</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ date('d M Y', strtotime($order->booking_date)) }}</td>
                                            <td>
                                                {{ $order['customers']['name'] }}<br>
                                                {{ $order['customers']['phone'] }}
                                            </td>
                                            <td class="text-capitalize">

                                                @if ($order['order_status'] == 'confirmed')
                                                    <span class="badge badge-soft-success fz-12">
                                                        {{ translate('confirmed') }}
                                                    </span>
                                                @elseif($order['order_status'] == 'processing')
                                                    <span class="badge badge-danger fz-12">
                                                        {{ translate('processing') }}
                                                    </span>
                                                @elseif($order['order_status'] == 'in-transit')
                                                    <span class="badge badge-danger fz-12">
                                                        {{ translate('out for pickup') }}
                                                    </span>
                                                @elseif($order['order_status'] == 'out_for_pickup')
                                                    <span class="badge badge-danger fz-12">
                                                        {{ translate('out for pickup') }}
                                                    </span>
                                                @elseif($order['order_status'] == 'delivered')
                                                    <span class="badge badge-soft-success fz-12">
                                                        {{ translate('delivered') }}
                                                    </span>
                                                @elseif($order['order_status'] == 'canceled')
                                                    <span class="badge badge-soft-success fz-12">
                                                        {{ translate('canceled') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-soft-success fz-12">
                                                        {{ $order['order_status'] }}
                                                    </span>
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
                            {{-- {{ $orders->links() }} --}}
                        </div>
                    </div>
                    {{-- @if (count($orders) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>



@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/datatables/dataTables.bootstrap4.min.js') }}">
    </script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>



    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
