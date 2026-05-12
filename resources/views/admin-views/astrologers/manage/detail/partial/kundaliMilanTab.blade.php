<div class="tab-pane fade show {{ empty($KundaliOrders) ? 'active' : '' }}" id="kundaliMilan" role="tabpanel"
    aria-labelledby="kundaliMilan-tab">
    <div class="table-responsive datatable-custom">
        <table id="datatable" style="text-align: left;"
            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
            <thead class="thead-light thead-50 text-capitalize">
                <tr>
                    <th>{{ translate('#') }}</th>
                    <th>{{ translate('id') }}</th>
                    <th>{{ translate('service_Type') }}</th>
                    <th>{{ translate('Order Date') }}</th>
                    <th>{{ translate('User Name') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th>{{ translate('Action') }}</th>
                </tr>
            </thead>
            <tbody id="set-rows">
                @foreach ($KundaliOrders as $Kuney => $kunval)
                <tr>
                    <td>{{ $Kuney + 1 }}</td>
                    <td>{{ $kunval['order_id']??'' }}</td>
                    <td>{{ ((($kunval['birthJournal_kundalimilan']['type']??'') == 'pro')?'Professional':'basic') }}
                    </td>
                    <td>{{ date('d/m/Y H:i', strtotime($kunval['created_at'])) }}
                    </td>
                    <td><b>{{ ($kunval['userData']['f_name']??'').' '.($kunval['userData']['l_name']??'') }}</b>
                        <p>{{ $kunval['userData']['phone']??'' }}
                        </p>
                    </td>
                    </td>
                    <td><span
                            class="badge badge-soft-{{ $kunval['status'] == 0 ? 'info' : ($kunval['status'] == 1 ? 'success' : 'danger') }}">
                            {{ $kunval['status'] == 0 ? 'Pending' : ($kunval['status'] == 1 ? 'Completed' : 'Canceled') }}
                        </span></td>
                    <td class="text-center"><a href="javascript:0"
                            data-malename="{{$kunval['name']}}"
                            data-femalename="{{$kunval['female_name']}}"
                            data-maledob="{{$kunval['bod']}}"
                            data-femaledob="{{$kunval['female_dob']}}"
                            data-maletime="{{ date('h:i A',strtotime($kunval['time'])) }}"
                            data-femaletime="{{ date('h:i A',strtotime($kunval['female_time']))}}"
                            data-malecountry="{{$kunval['country']['name']??''}}"
                            data-femalecountry="{{$kunval['country_female']['name']??''}}"
                            data-malelocation="{{$kunval['state']}}"
                            data-femalelocation="{{$kunval['female_place']}}"
                            onclick="kundalimilanOrderModal(this)">
                            <i class="tio-invisible"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive mt-4">
        <div class="d-flex justify-content-lg-end">
            {{ $KundaliOrders->appends(['kundli-page' => request('kundli-page')])->links() }}
        </div>
    </div>
    @if (count($KundaliOrders) == 0)
    <div class="text-center p-4">
        <img class="mb-3 w-160"
            src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
    </div>
    @endif
</div>