@extends('layouts.back-end.app-seller')

@section('title', translate('Order_by_Prasaad'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <style>
        .ui-tooltip {
            max-width: 300px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            color: #333;
        }
    </style>
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('Order_by_Prasad') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
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
                                        <th>{{ translate('pooja_name') }}</th>

                                        <th>{{ translate('Total_order`s') }}</th>
                                        <th>{{ translate('Completed_date') }}</th>
                                        <th>{{ translate('status') }}</th>
                                        <th>{{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
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


                                            <td>{{ $order->total_orders }}</td>
                                            <td>{{ date('d M Y', strtotime($order->booking_date)) }}</td>
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
                                            <td>
                                                {{-- @if ($order->pooja_status == 1) --}}
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-warning btn-sm square-btn"
                                                        title="Order Details" target="_blank"
                                                        href="{{ route('vendor.orders.prashad.prashadamDetails', ['service_id' => $order->service_id, 'date' => $order->booking_date]) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="12" viewBox="0 0 14 12" fill="none"
                                                            class="svg replaceds-svg">
                                                            <path
                                                                d="M6.79584 3.75937C6.86389 3.75234 6.93195 3.75 7 3.75C8.2882 3.75 9.33333 4.73672 9.33333 6C9.33333 7.24219 8.2882 8.25 7 8.25C5.68993 8.25 4.66667 7.24219 4.66667 6C4.66667 5.93437 4.6691 5.86875 4.67639 5.80313C4.90243 5.90859 5.16493 6 5.44445 6C6.30243 6 7 5.32734 7 4.5C7 4.23047 6.90521 3.97734 6.79584 3.75937ZM11.6813 2.63906C12.8188 3.65625 13.5795 4.85391 13.9392 5.71172C14.0194 5.89687 14.0194 6.10312 13.9392 6.28828C13.5795 7.125 12.8188 8.32266 11.6813 9.36094C10.5365 10.3875 8.96389 11.25 7 11.25C5.03611 11.25 3.46354 10.3875 2.31924 9.36094C1.18174 8.32266 0.42146 7.125 0.059818 6.28828C0.0203307 6.19694 0 6.09896 0 6C0 5.90104 0.0203307 5.80306 0.059818 5.71172C0.42146 4.85391 1.18174 3.65625 2.31924 2.63906C3.46354 1.61344 5.03611 0.75 7 0.75C8.96389 0.75 10.5365 1.61344 11.6813 2.63906ZM7 2.625C5.06771 2.625 3.5 4.13672 3.5 6C3.5 7.86328 5.06771 9.375 7 9.375C8.93229 9.375 10.5 7.86328 10.5 6C10.5 4.13672 8.93229 2.625 7 2.625Z"
                                                                fill="#0177CD"></path>
                                                        </svg>
                                                    </a>



                                                </div>
                                                {{-- @endif --}}
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
                    @if (count($orders) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <script>
        $('#date_type').change(function(e) {
            e.preventDefault();

            var value = $(this).val();
            if (value == 'custom_date') {
                $('#from-to-div').show();
            } else {
                $('#from-to-div').hide();
            }
        });
    </script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script>
        new DataTable('#example', {
            layout: {
                topStart: {
                    buttons: ['print']
                }
            }
        });
    </script>
@endpush
