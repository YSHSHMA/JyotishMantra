<div class="table-responsive">
    <table id="datatable" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
        <thead class="thead-light thead-50 text-capitalize">
            <tr>
                <th>{{ translate('SL') }}</th>
                <th>{{ translate('amount') }}</th>
                <th>{{ translate('request_time') }}</th>
                <th>{{ translate('status') }}</th>
                <th>{{ translate('request_update') }}</th>
                <th class="text-center">{{ translate('action') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($withdrawRequests->count() > 0)
                @foreach ($withdrawRequests as $key => $withdrawRequest)
                    <tr>
                        <td>{{ $withdrawRequests->firstitem() + $key }}</td>
                        <td>{{ currencyConverter($withdrawRequest['amount']) }}</td>
                        <td>{{ date('F jS, Y', strtotime($withdrawRequest->created_at)) }}</td>
                        <td>
                            @if ($withdrawRequest->approved == 0)
                                <label class="badge badge-soft--primary">{{ translate('pending') }}</label>
                            @elseif($withdrawRequest->approved == 1)
                                <label class="badge badge-soft-success">{{ translate('approved') }}</label>
                            @else
                                <label class="badge badge-soft-danger">{{ translate('denied') }}</label>
                            @endif
                        </td>
                        <td>
                            @if ($withdrawRequest->approved == 1)
                                <label class="badge badge-soft-success">
                                    {{ date('F jS, Y', strtotime($withdrawRequest->updated_at)) }}</label>
                            @else($withdrawRequest->approved==2)
                                <label class="badge badge-soft-danger">
                                    {{ date('F jS, Y', strtotime($withdrawRequest->updated_at)) }}</label>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($withdrawRequest->approved == 0)
                                <button
                                    id="{{ route('vendor.business-settings.withdraw.close', [$withdrawRequest['id']]) }}"
                                    data-action="{{ route('vendor.business-settings.withdraw.close', [$withdrawRequest['id']]) }}"
                                    class="btn btn--primary btn-sm close-request">
                                    {{ translate('close') }}
                                </button>
                            @else
                                <span class="btn btn--primary btn-sm disabled">
                                    {{ translate('close') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <td colspan="5" class="text-center">
                    <img class="mb-3 w-160"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('image_description') }}">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </td>
            @endif
        </tbody>
    </table>
</div>
<div class="table-responsive mt-4">
    <div class="px-4 d-flex justify-content-lg-end">
        {{ $withdrawRequests->links() }}
    </div>
</div>
