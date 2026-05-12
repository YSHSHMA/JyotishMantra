@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Order_history'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
@endpush
@section('content')

    {{-- pooja member modal --}}
    <div class="modal fade" id="pooja-member-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pooja Members</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tbody id="memberTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- pooja Order modal --}}
    <div class="modal fade" id="pooja-order-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pooja Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">

                        <thead>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Prashad</th>
                            <th>Order Date</th>
                        </thead>

                        <tbody id="orderTB">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- main page --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                    alt="">
                {{ translate('Order_history') }}
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mb-5">
                    <ul class="nav nav-tabs flex-wrap page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link "
                                href="{{ route('admin.astrologers.manage.detail.overview', $id) }}">Overview</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'order'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.order', $id) }}">Order</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'service'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.service', $id) }}">Service</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'setting'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.setting', $id) }}">Setting</a>
                            </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'transaction'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.transaction', $id) }}">Transaction</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.astrologers.manage.detail.transaction.history', $id) }}">Transaction History</a>
                        </li>
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'review'))
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('admin.astrologers.manage.detail.review', $id) }}">Review</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.astrologers.manage.detail.history', $id) }}">History</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content mt-5">
                    <div class="tab-pane fade show active" id="order">
                        <div class="row pt-2">
                            <div class="col-md-12">
                                <div class="card w-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ translate('History_order_list') }}</h5>
                                    </div>


                                    <div class="row">

                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                                @if(isset($historyData['counselling']))
                                                <li class="nav-item col-4" role="presentation">
                                                    <button class="nav-link w-100 active" id="counselling-tab" data-toggle="pill"
                                                        data-target="#counselling" type="button" role="tab"
                                                        aria-controls="counselling" aria-selected="true">Counselling
                                                        Orders</button>
                                                </li>
                                                @endif
                                                @if(isset($historyData['pooja']))
                                                <li class="nav-item col-4" role="presentation">
                                                    <button class="nav-link w-100 active" id="pooja-tab" data-toggle="pill"
                                                        data-target="#pooja" type="button" role="tab"
                                                        aria-controls="pooja" aria-selected="true">Pooja
                                                        Orders</button>
                                                </li>
                                                @endif
                                                @if(isset($historyData['chadhava']))
                                                <li class="nav-item col-4" role="presentation">
                                                    <button class="nav-link w-100" id="chadhava-tab" data-toggle="pill"
                                                        data-target="#chadhava" type="button" role="tab"
                                                        aria-controls="chadhava" aria-selected="true">Chadhava
                                                        Orders</button>
                                                </li>
                                                @endif
                                                @if(isset($historyData['offlinepooja']))
                                                <li class="nav-item col-4" role="presentation">
                                                    <button class="nav-link w-100" id="offlinepooja-tab" data-toggle="pill"
                                                        data-target="#offlinepooja" type="button" role="tab"
                                                        aria-controls="offlinepooja" aria-selected="true">Offline Pooja
                                                        Orders</button>
                                                </li>
                                                @endif
                                                @if(isset($historyData['KundaliOrders']))
                                                <li class="nav-item col-4" role="presentation">
                                                    <button class="nav-link w-100" id="KundaliOrders-tab" data-toggle="pill"
                                                        data-target="#KundaliOrders" type="button" role="tab"
                                                        aria-controls="KundaliOrders" aria-selected="true">Kundali Milan Orders</button>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="tab-content" id="pills-tabContent">
                                                @if(isset($historyData['counselling']))
                                                    <div class="tab-pane fade show active" id="counselling" role="tabpanel"
                                                        aria-labelledby="counselling-tab">
                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>

                                                                        <th style="width: 25%">{{ translate('name') }}
                                                                        </th>

                                                                        <th>{{ translate('date_&_time') }}</th>
                                                                       
                                                                        <th>{{ translate('status') }}</th>
                                                                      
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="set-rows">
                                                                    @foreach ($historyData['counselling'] as $counsellingKey => $counsellingOrder)
                                                                        <tr>
                                                                            <td>{{ $counsellingKey + 1 }}</td>
                                                                            <td>{{ Str::limit($counsellingOrder['services']['name'], 70) }}
                                                                            </td>
                                                                            <td>{{ !empty($counsellingOrder['order_completed'])?$counsellingOrder['order_completed']:$counsellingOrder['order_canceled']  }}
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="text-success">{{ translate($counsellingOrder['status']==1?'Completed':'Canceled') }}</span>
                                                                            </td>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive mt-4">
                                                            <div class="d-flex justify-content-lg-end">
                                                                {{-- {{ $ChadhavaOrder->links() }} --}}
                                                            </div>
                                                        </div>
                                                        @if (count($historyData['counselling']) == 0)
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                               @if(isset($historyData['pooja']))
                                                    <div class="tab-pane fade show active" id="pooja" role="tabpanel"
                                                        aria-labelledby="pooja-tab">
                                                        <div class="table-responsive">
                                                            <table id="myTable"
                                                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>

                                                                        <th style="width: 25%">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('category') }}</th>
                                                                        <th>{{ translate('date_&_time') }}</th>
                                                                        <th>{{ translate('complete_date') }}</th>
                                                                        <th>{{ translate('status') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="set-rows">
                                                                    @foreach ($historyData['pooja'] as $poojaKey => $poojaOrder)
                                                                        <tr>
                                                                            <td>{{ $poojaKey + 1 }}</td>

                                                                            <td>
                                                                                @if ($poojaOrder->type == 'pooja')
                                                                                    {{ Str::limit($poojaOrder['services']['name'], 70) }}
                                                                                @else
                                                                                    {{ Str::limit($poojaOrder['vippoojas']['name'], 70) }}
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($poojaOrder->type == 'pooja')
                                                                                    {{ $poojaOrder['services']['category']['name'] }}
                                                                                @elseif($poojaOrder->type == 'vip')
                                                                                    {{ translate('vip') }}
                                                                                @else
                                                                                    {{ translate('anushthan') }}
                                                                                @endif

                                                                            </td>
                                                                            <td>{{ !empty($poojaOrder['booking_date']) ? date('d/m/Y', strtotime($poojaOrder['booking_date'])) : '' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ !empty($poojaOrder['order_completed']) ? date('d/m/Y', strtotime($poojaOrder['order_completed'])) : date('d/m/Y', strtotime($poojaOrder['order_canceled'])) }}

                                                                            </td>
                                                                            <td><span
                                                                                    class="text-success">{{ translate($poojaOrder['status']==1?'Completed':'Canceled') }}</span>
                                                                            </td>

                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive mt-4">
                                                            <div class="d-flex justify-content-lg-end">
                                                                {{-- {{ $poojaOrders->links() }} --}}
                                                            </div>
                                                        </div>
                                                        @if (count($historyData['pooja']) == 0)
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($historyData['chadhava']))
                                                    <div class="tab-pane fade show" id="chadhava" role="tabpanel"
                                                        aria-labelledby="chadhava-tab">
                                                        <div class="table-responsive datatable-custom">
                                                            <table id="datatable" style="text-align: left;"
                                                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>

                                                                        <th style="width: 25%">{{ translate('name') }}
                                                                        </th>

                                                                        <th>{{ translate('date_&_time') }}</th>
                                                                        <th style="width: 15%">{{ translate('venue') }}
                                                                        </th>
                                                                        <th>{{ translate('completed_date') }}</th>
                                                                        <th>{{ translate('status') }}</th>
                                                                      
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="set-rows">
                                                                    @foreach ($historyData['chadhava'] as $chadhavaKey => $ChadhavaOrder)
                                                                        <tr>
                                                                            <td>{{ $chadhavaKey + 1 }}</td>
                                                                            <td>{{ Str::limit($ChadhavaOrder['chadhava']['name'], 70) }}
                                                                            </td>
                                                                            <td>{{ !empty($ChadhavaOrder['booking_date']) ? date('d/m/Y', strtotime($ChadhavaOrder['booking_date'])) : '' }}
                                                                            </td>
                                                                            <td>
                                                                                @if ($ChadhavaOrder->type == 'chadhava')
                                                                                    {{ $ChadhavaOrder['chadhava']['chadhava_venue'] }}
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                {{ !empty($ChadhavaOrder['order_completed']) ? date('d/m/Y', strtotime($ChadhavaOrder['order_completed'])) : date('d/m/Y', strtotime($ChadhavaOrder['order_canceled'])) }}

                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                class="text-success">{{ translate($ChadhavaOrder['status']==1?'Completed':'Canceled') }}</span>
                                                                            </td>
                                                                            
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive mt-4">
                                                            <div class="d-flex justify-content-lg-end">
                                                                {{-- {{ $ChadhavaOrder->links() }} --}}
                                                            </div>
                                                        </div>
                                                        @if (count($historyData['chadhava']) == 0)
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($historyData['offlinepooja']))
                                                    <div class="tab-pane fade show" id="offlinepooja" role="tabpanel"
                                                        aria-labelledby="offlinepooja-tab">
                                                        <div class="table-responsive">
                                                            <table id="myTable"
                                                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>

                                                                        <th style="width: 40%">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('date_&_time') }}</th>
                                                                        {{-- <th style="width: 15%">{{ translate('venue') }}</th> --}}
                                                                        <th>{{ translate('status') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="set-rows">
                                                                    @foreach ($historyData['offlinepooja'] as $offlinepoojaKey => $offlinepoojaOrder)
                                                                        <tr>
                                                                            <td>{{ $offlinepoojaKey + 1 }}</td>

                                                                            <td>
                                                                                {{ Str::limit($offlinepoojaOrder['offlinePooja']['name'], 70) }}
                                                                            </td>
                                                                            <td>
                                                                                {{ !empty($offlinepoojaOrder['order_completed']) ? date('d/m/Y', strtotime($offlinepoojaOrder['order_completed'])) : date('d/m/Y', strtotime($offlinepoojaOrder['order_canceled'])) }}

                                                                            </td>
                                                                            <td><span
                                                                                    class="text-success">{{ translate($offlinepoojaOrder['status']==1?'Completed':'Canceled') }}</span>
                                                                            </td>

                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive mt-4">
                                                            <div class="d-flex justify-content-lg-end">
                                                                {{-- {{ $offlinepoojaOrders->links() }} --}}
                                                            </div>
                                                        </div>
                                                        @if (count($historyData['offlinepooja']) == 0)
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if(isset($historyData['KundaliOrders']))
                                                    <div class="tab-pane fade show" id="KundaliOrders" role="tabpanel"
                                                        aria-labelledby="KundaliOrders-tab">
                                                        <div class="table-responsive">
                                                            <table id="myTable"
                                                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                                                <thead class="thead-light thead-50 text-capitalize">
                                                                    <tr>
                                                                        <th>{{ translate('#') }}</th>

                                                                        <th style="width: 40%">{{ translate('name') }}
                                                                        </th>
                                                                        <th>{{ translate('booking_date') }}</th>
                                                                        <th>{{ translate('date_&_time') }}</th>
                                                                        <th>{{ translate('status') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="set-rows">
                                                                    @foreach ($historyData['KundaliOrders'] as $kunKey => $kundlival)
                                                                        <tr>
                                                                            <td>{{ $kunKey + 1 }}</td>

                                                                            <td>
                                                                                {{ (($kundlival['birthJournal_kundalimilan']['type']??'') == 'pro')?'Professional':'Basic' }}
                                                                            </td>
                                                                             <td>
                                                                                {{ date('d/m/Y h:i A', strtotime($kundlival['created_at'])) }}
                                                                            </td>
                                                                            <td>
                                                                                {{ date('d/m/Y h:i A', strtotime($kundlival['updated_at'])) }}
                                                                            </td>
                                                                            <td>
                                                                                <span class="text-success">{{ translate($kundlival['milan_verify']==1?'Completed':'Canceled') }}</span>
                                                                            </td>

                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive mt-4">
                                                            <div class="d-flex justify-content-lg-end">
                                                                {{ $historyData['KundaliOrders']->appends(['kundli-page' => request('kundli-page')])->links() }}
                                                            </div>
                                                        </div>
                                                        @if (count($historyData['KundaliOrders']) == 0)
                                                            <div class="text-center p-4">
                                                                <img class="mb-3 w-160"
                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                                    alt="">
                                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    {{-- pooja member modal --}}
    <script>
        function poojaMemberModal(that) {
            $('#memberTB').html('');
            var list = "";
            var membersData = $(that).data('members');
            var gotra = $(that).data('gotra');
            if (membersData.includes('|')) {
                var memberParts = membersData.split('|');
                $.each(memberParts, function(key, value) {
                    value = value.replace(/[\[\]"]/g, '');
                    list += `<tr><td>Order- ${key + 1}</td><td>${value}</td></tr>`;
                });
            }
            list += `<tr><td>Gotra</td><td>${gotra}</td></tr>`;
            $('#memberTB').append(list);
            $('#pooja-member-modal').modal('show');
        }

        function poojaOrderModal(that) {
            var serviceId = $(that).data('serviceid');
            var bookingDate = $(that).data('bookingdate');

            $.ajax({
                url: "{{ url('admin/astrologers/manage/order-data') }}",
                type: 'GET',
                data: {
                    serviceId: serviceId,
                    bookingDate: bookingDate
                },
                success: function(data) {

                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        const options = {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        };
                        return date.toLocaleString('en-US', options).replace(',', '');
                    }

                    $.each(data.data, function(key, value) {
                        $('#orderTB').html(`
                                <tr>
                                    <td>${value.order_id}</td>
                                    <td>${value.customers.f_name} ${value.customers.l_name}</td>
                                    <td>${value.is_prashad==1?'Yes':'No'}</td>
                                    <td>${formatDate(value.created_at)}</td>
                                </tr> 
                                `);
                    });
                    $('#pooja-order-modal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch order details.');
                }
            });

        }
    </script>

    {{-- pooja member modal --}}
    <script>
        function consultationUser(that) {
            $('#consultationTB').html('');
            var consultationList = "";
            var name = $(that).data('name');
            var gender = $(that).data('gender');
            var mob = $(that).data('mob');
            var dob = $(that).data('dob');
            var time = $(that).data('time');
            var country = $(that).data('country');
            var city = $(that).data('city');

            consultationList +=
                `<tr><td>Name</td><td>${name}</td></tr><tr><td>Gender</td><td>${gender}</td></tr><tr><td>DOB</td><td>${dob}</td></tr><tr><td>Birth Time</td><td>${time}</td></tr><tr><td>Country</td><td>${country}</td></tr><tr><td>City</td><td>${city}</td></tr>`;

            $('#consultationTB').append(consultationList);
            $('#consultation-user-modal').modal('show');
        }
    </script>
@endpush