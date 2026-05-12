<div class="tab-pane fade show {{ empty($poojaOrders) ? 'active' : '' }}" id="consultation" role="tabpanel"
    aria-labelledby="consultation-tab">
    <div class="table-responsive datatable-custom">
        <table id="datatable" style="text-align: left;"
            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
            <thead class="thead-light thead-50 text-capitalize">
                <tr>
                    <th>{{ translate('#') }}</th>
                    <th>{{ translate('id') }}</th>
                    <th>{{ translate('service_Name') }}</th>
                    <th>{{ translate('category') }}</th>
                    <th>{{ translate('Order Date') }}</th>
                    <th>{{ translate('User Name') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th>{{ translate('Action') }}</th>
                </tr>
            </thead>
            <tbody id="set-rows">
                @foreach ($consultationOrders as $consultationKey => $consultationOrder)
                    <tr>
                        <td>{{ $consultationKey + 1 }}</td>
                        <td>{{ $consultationOrder['order_id'] }}</td>
                        <td>{{ $consultationOrder['services']['name'] }}
                        </td>
                        <td>{{ $consultationOrder['services']['category']['name'] }}
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($consultationOrder['created_at'])) }}
                        </td>
                        <td><b>{{ !empty($consultationOrder['counselling_user']['name'])?$consultationOrder['counselling_user']['name']:'' }}</b>
                            <p>{{ !empty($consultationOrder['counselling_user']['mobile'])?$consultationOrder['counselling_user']['mobile']:'' }}
                            </p>
                        </td>
                        </td>
                        <td><span
                                class="badge badge-soft-{{ $consultationOrder['status'] == 0 ? 'info' : ($consultationOrder['status'] == 1 ? 'success' : 'danger') }}">
                                {{ $consultationOrder['status'] == 0 ? 'Pending' : ($consultationOrder['status'] == 1 ? 'Completed' : 'Canceled') }}
                            </span></td>
                        <td><a href="javascript:0" data-name="{{ !empty($consultationOrder['counselling_user']['name'])?$consultationOrder['counselling_user']['name']:'' }}"
                                data-gender="{{ !empty($consultationOrder['counselling_user']['gender'])?$consultationOrder['counselling_user']['gender']:'' }}"
                                data-mob="{{ !empty($consultationOrder['counselling_user']['mobile'])?$consultationOrder['counselling_user']['mobile']:'' }}"
                                data-dob="{{ !empty($consultationOrder['counselling_user']['dob'])? $consultationOrder['counselling_user']['dob']:'' }}"
                                data-time="{{ !empty($consultationOrder['counselling_user']['time'])?$consultationOrder['counselling_user']['time']:'' }}"
                                data-country="{{ !empty($consultationOrder['counselling_user']['country'])?$consultationOrder['counselling_user']['country']:'' }}"
                                data-city="{{ !empty($consultationOrder['counselling_user']['city'])?$consultationOrder['counselling_user']['city']:'' }}"
                                onclick="consultationUser(this)">Customer
                                Info</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive mt-4">
        <div class="d-flex justify-content-lg-end">
            {{ $consultationOrders->links() }}
        </div>
    </div>
    @if (count($consultationOrders) == 0)
        <div class="text-center p-4">
            <img class="mb-3 w-160"
                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
        </div>
    @endif
</div>
