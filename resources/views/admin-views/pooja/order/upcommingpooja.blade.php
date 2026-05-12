@extends('layouts.back-end.app')

@section('title', translate('Upcomming_Pooja'))
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
<style>
    /* Optional: Style for the tooltip */
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
                {{ translate('Upcomming_Pooja') }}
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
            </div>
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
                            <table id="myTable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Pooja_Id') }}</th>
                                        <th>{{ translate('pooja_name') }}</th>
                                        <th>{{ translate('pooja_category') }}</th>
                                        <th>{{ translate('pooja_date_time') }}</th>
                                        <th>{{ translate('Pooja_venue') }}</th>
                                        <th>{{ translate('no_of_orders') }}</th>
                                        <th>{{ translate('total_of_members') }}</th>
                                        <th>{{ translate('total_amount') }}</th>
                                        <th>{{ translate('pandit_name') }}</th>
                                        <th>{{ translate('action') }}</th>
                                        <th class="text-center"> {{ translate('Video_send_status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>## {{ $order->service_id }}</td>
                                            <td>{{ Str::limit($order['services']['name'],30) }}</td>
                                            <td>{{ Str::limit($order['services']['category']['name'],30) }}</td>
                                            <td>{{ date('d M Y', strtotime($order->booking_date))}} {{ date('h:i A', strtotime($order['services']['pooja_time'])) }}</td>
                                            @php
                                             $poojaVenue='';
                                                $venue = json_decode($order['services']['pooja_venue']);
                                                if (is_array($venue) || is_object($venue)) {
                                                    foreach ($venue as $address) {
                                                        $poojaVenue= $address;
                                                    }
                                                }
                                            @endphp
                                            <td>{{ Str::limit($address,20) }}</td>
                                            <td>{{ $order->total_orders }}</td>
                                            <td>
                                                @php
                                                    $member_count = 0;
                                                    $members = explode('|',$order->members);
                                                    foreach($members as $memb){
                                                        if($memb != null){
                                                            $member_count += count(json_decode($memb));
                                                        }
                                                    }
                                                    
                                                @endphp
                                                @if ($order->members != null)
                                                @php
                                                    $members_clean = preg_replace(["/\[|\]/", "/'([^']+)'/"], "", $order->members);
                                                    $members_array = explode(',', $members_clean);
                                                @endphp
                                               
                                                  <span class="tio-user nav-icon" title="{{ str_replace(',', '<br>', str_replace('"', '', implode(',', $members_array))) }}"> </span>{{ $member_count }}
                                                @else
                                                    <span class="badge badge-soft-danger">No Members</span>
                                                @endif
                                            </td>
                                            
                                            <td>₹{{ $order->total_amount }}</td>
                                            <td>
                                                @if ($order->pandit_assign != null)
                                                    <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                                @else
                                                    <span class="badge badge-soft-danger">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-info btn-sm square-btn" target="_blank" title="All order list" href="{{ route('admin.pooja.orders.AllSingleOrder', ['service_id' => $order->service_id, 'is_completed' => 0]) }}">
                                                        <i class="tio-chart-bar-4 nav-icon"></i>                                                    
                                                    </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn" title="Order Details" target="_blank" href="{{ route('admin.pooja.orders.SingleOrderdetails', ['service_id' => $order->service_id, 'is_completed' => 0]) }}">
                                                        <i class="tio-shopping-cart-outlined nav-icon"></i>                                                    
                                                    </a>
                                            </diV>
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-{{ $order->status == 0 ? 'primary' : ($order->status == 1 ? 'success' : 'danger') }}">{{ $order->status == 0 ? 'In Progress' : ($order->status == 1 ? 'Send' : 'Canceled') }}</span>
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
