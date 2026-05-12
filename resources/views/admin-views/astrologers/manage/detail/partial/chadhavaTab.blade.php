<div class="table-responsive datatable-custom">
    <table id="datatable" style="text-align: left;"
        class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
        <thead class="thead-light thead-50 text-capitalize">
            <tr>
                <th>{{ translate('#') }}</th>

                <th style="width: 25%">{{ translate('name') }}
                </th>

                <th>{{ translate('date_&_time') }}</th>
                <th>{{ translate('no_of_order') }}</th>
                <th style="width: 15%">{{ translate('venue') }}
                </th>
                <th>{{ translate('status') }}</th>
                <th>{{ translate('action') }}</th>
            </tr>
        </thead>
        <tbody id="set-rows">
            @foreach ($ChadhavaOrder as $chadhavaKey => $ChadhavaOrder)
                <tr>
                    <td>{{ $chadhavaKey + 1 }}</td>
                    <td>{{ Str::limit($ChadhavaOrder['chadhava']['name'], 70) }}
                    </td>
                    <td>{{ !empty($ChadhavaOrder['booking_date']) ? date('d/m/Y', strtotime($ChadhavaOrder['booking_date'])) : '' }}
                    </td>
                    <td>{{ !empty($ChadhavaOrder['booking_count']) ? $ChadhavaOrder['booking_count'] : '' }}
                    </td>
                    <td>
                        @if ($ChadhavaOrder->type == 'chadhava')
                            {{ $ChadhavaOrder['chadhava']['chadhava_venue'] }}
                        @endif
                    </td>
                    <td>{{ empty($ChadhavaOrder['pooja_video']) ? 'In progress' : 'Sent' }}
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:0"
                                class="btn btn-outline-primary btn-sm square-btn
                                "title="{{ translate('view') }}"
                                data-members="{{ $ChadhavaOrder['members'] }}"
                                data-gotra="{{ $ChadhavaOrder['gotra'] }}" onclick="chadhavaMemberModal(this)"><i
                                    class="tio-user"></i></a>

                            <a href="javascript:0" class="btn btn-outline-info btn-sm square-btn"
                                title="{{ translate('view') }}"
                                data-serviceid="{{ $ChadhavaOrder['chadhava']['id'] }}"
                                data-bookingdate="{{ $ChadhavaOrder['booking_date'] }}"
                                onclick="chadhavaOrderModal(this)"><i class="tio-truck"></i></a>
                        </div>
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
{{-- @if (count($ChadhavaOrder) == 0)
    <div class="text-center p-4">
        <img class="mb-3 w-160"
            src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
            alt="">
        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
    </div>
@endif --}}
