<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Trust_transaction') }}</h5>
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
                                <input type="hidden" name='type' value='trust_tran'>
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
                                <th>{{ translate('Tran_ID') }}</th>
                                <th>{{ translate('type') }}</th>
                                <th>{{ translate('TXN_id') }}</th>
                                <th>{{ translate('status') }}</th>
                                <th>{{ translate('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through items -->
                            @foreach($amount_transaction as $key => $items)
                            <tr>
                                <td>{{$amount_transaction->firstItem()+$key}}</td>
                                <td>{{ $items['trans_id'] }}</td>
                                <td>{{ ((($items['type']??'') == 'ad_approval')?'Ads Approval':'Withdrawal') }}</td>
                                <td>{{ ($items['transaction_id']??'') }}</td>
                                <td>{{ (($items['amount_status']??'') == 1)?'Success':'Pending' }}</td>
                                <td>₹ {{ ($items['amount']??'') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <fbody>
                            <th colspan='2' class='font-weight-bold'>Approval</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type','ad_approval')->where('amount_status',1)->where('trust_id',$id)->sum('amount') }}</th>
                            <th colspan='2' class='font-weight-bold'>Withdrawal</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type','withdrawal')->where('amount_status',1)->where('trust_id',$id)->sum('amount') }}</th>
                        </fbody>
                    </table>
                </div>
            </div>
            <!-- Pagination for trust list -->
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-lg-end">
                    {!! $amount_transaction->links() !!}
                </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($amount_transaction) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
        </div>
    </div>


</div>