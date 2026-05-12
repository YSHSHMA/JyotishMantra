<div class="tab-pane fade show {{ empty($offlinepoojaOrders) ? 'active' : '' }}" id="offlinepooja" role="tabpanel"
    aria-labelledby="offlinepooja-tab">
    <div class="table-responsive datatable-custom">
        <table id="datatable" style="text-align: left;"
            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
            <thead class="thead-light thead-50 text-capitalize">
                <tr>
                    <th>{{ translate('#') }}</th>
                    <th>{{ translate('id') }}</th>
                    <th>{{ translate('service_Name') }}</th>
                    <th>{{ translate('Order Date') }}</th>
                    <th>{{ translate('User Name') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th>{{ translate('Action') }}</th>
                </tr>
            </thead>
            <tbody id="set-rows">
                @foreach ($offlinepoojaOrders as $offlinepoojaKey => $offlinepoojaOrder)
                    <tr>
                        <td>{{ $offlinepoojaKey + 1 }}</td>
                        <td>{{ $offlinepoojaOrder['order_id'] }}</td>
                        <td>{{ $offlinepoojaOrder['offlinePooja']['name'] }}
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($offlinepoojaOrder['created_at'])) }}
                        </td>
                        <td><b>{{ $offlinepoojaOrder['customers']['f_name'].' '.$offlinepoojaOrder['customers']['l_name'] }}</b>
                            <p>{{ $offlinepoojaOrder['customers']['phone'] }}
                            </p>
                        </td>
                        </td>
                        <td><span
                                class="badge badge-soft-{{ $offlinepoojaOrder['status'] == 0 ? 'info' : ($offlinepoojaOrder['status'] == 1 ? 'success' : 'danger') }}">
                                {{ $offlinepoojaOrder['status'] == 0 ? 'Pending' : ($offlinepoojaOrder['status'] == 1 ? 'Completed' : 'Canceled') }}
                            </span></td>
                        <td class="text-center"><a href="javascript:0" data-venueaddress="{{$offlinepoojaOrder['venue_address']}}"
                                data-bookingdate="{{ date('d F Y', strtotime($offlinepoojaOrder['booking_date']))}}"
                                data-landmark="{{ $offlinepoojaOrder['landmark']}}"
                                onclick="offlinepoojaOrderModal(this)"><i
                                class="tio-invisible"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive mt-4">
        <div class="d-flex justify-content-lg-end">
            {{ $offlinepoojaOrders->links() }}
        </div>
    </div>
    @if (count($offlinepoojaOrders) == 0)
        <div class="text-center p-4">
            <img class="mb-3 w-160"
                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
        </div>
    @endif
</div>
