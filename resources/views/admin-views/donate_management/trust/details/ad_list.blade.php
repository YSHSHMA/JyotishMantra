<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Ads_List') }}</h5>
    </div>

    <div class="my-5">
        <div class="col-md-12">
            <div class="px-3 py-4">
                <!-- Search bar -->
                <div class="row align-items-center">
                    <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                    </div>
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-custom input-group-merge">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                <input type="hidden" name='type' value='adlist'>
                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Table displaying trust  -->
            <div class="text-start">
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Ads_ID') }}</th>
                                <th>{{ translate('name') }}</th>
                                <th>{{ translate('category') }}</th>
                                <th>{{ translate('trust_name') }}</th>
                                <th>{{ translate('Purpose_name') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Date_Info') }}</th>
                                <th>{{ translate('status') }}</th>
                            </tr>
                        </thead>
                        @if($ads_list && count($ads_list) > 0)
                        <tbody>
                            @foreach($ads_list as $key => $items)
                            <tr>
                                <td>{{$ads_list->firstItem()+$key}}</td>
                                <td><a href="{{route('admin.donate_management.ad_trust.ads-details',[$items['id']])}}" class='font-weight-bold text-secondary'>{{ $items['ads_id'] }}</a></td>
                                <td>{{ ($items['name']??'') }}</td>
                                <td>{{ ($items['category']['name']??'') }}</td>
                                <td>{{ ($items['Trusts']['trust_name']??'') }}</td>
                                <td>{{ ($items['Purpose']['name']??'') }}</td>
                                <td>₹ {{ ($items['approve_amount']??'') }}</td>
                                <td>
                                    <span>Created: {{ date('d M,Y h:i A',strtotime($items['created_at']??'')) }}</span><br>
                                    <span>Req. Date:
                                        @if(!empty($items['req_send_date']) && $items['req_send_date'] !== '0000-00-00 00:00:00')
                                        {{ date('d M,Y h:i A',strtotime($items['req_send_date']??'')) }}
                                        @else
                                        -
                                        @endif
                                    </span><br>
                                    <span>Pay. Date:
                                        @if(!empty($items['req_amount_received']) && $items['req_amount_received'] !== '0000-00-00 00:00:00')
                                        {{ date('d M,Y h:i A',strtotime($items['req_amount_received']??'')) }}
                                        @else
                                        -
                                        @endif
                                    </span><br>
                                </td>
                                <td>{{ (($items['is_approve']??'') == 1)?'Success':'Pending' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <fbody>
                            <th colspan='6' class='font-weight-bold'>Total</th>
                            <th class='font-weight-bold'>₹ {{ \App\Models\DonateAllTransaction::where('type','ad_approval')->where('amount_status',1)->where('trust_id',$id)->sum('amount') }}</th>
                            <th class='font-weight-bold'></th>
                            <th class='font-weight-bold'></th>
                        </fbody>
                        @endif
                    </table>
                </div>
            </div>
            <!-- Pagination for trust list -->
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-lg-end">
                    {!! $ads_list->links() !!}
                </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($ads_list) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
        </div>
    </div>


</div>