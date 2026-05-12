<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Donation_transaction') }}</h5>
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
                                <input type="hidden" name='type' value='donate_trust'>
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
                                <th>{{ translate('User_Info') }}</th>
                                <th>{{ translate('TXN_id') }}</th>
                                <th>{{ translate('donated_date') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('admin_commission') }}</th>
                                <th>{{ translate('Final_amount') }}</th>
                                <th>{{ translate('option') }}</th>
                            </tr>
                        </thead>
                        @if($donate_trust_transaction && count($donate_trust_transaction) > 0)
                        <tbody>
                            <!-- Loop through items -->
                            @foreach($donate_trust_transaction as $key => $items)
                            <tr>
                                <td>{{$donate_trust_transaction->firstItem()+$key}}</td>
                                <td>{{ $items['trans_id'] }}</td>
                                <td>
                                    <span>{{ ($items['users']['name']??'') }}</span><br>
                                    <span>{{ ($items['users']['phone']??'') }}</span><br>
                                    <span>{{ ($items['users']['email']??'') }}</span><br>
                                </td>
                                <td>{{ ($items['transaction_id']??'') }}</td>
                                <td>{{ date('d M,Y h:i A',strtotime($items['created_at']??'')) }}</td>
                                <td>₹{{ ($items['amount']??'') }}</td>
                                <td>₹{{ ($items['admin_commission']??'') }}</td>
                                <td>₹{{ ($items['final_amount']??'') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}" href="{{ route('admin.donate_management.donated.view',['id'=>$items['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('invoice') }}" target="_blank" href="{{ route('donate-create-pdf-invoice', [$items['id']]) }}">
                                            <i class="tio-arrow_large_downward">arrow_large_downward</i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('80G') }}" target="_blank" href="{{ url('api/v1/donate/twoal-a-certificate', [$items['id']]) }}">
                                            <i class="tio-file_text">file_text</i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <fbody>
                            <th colspan='5' class='font-weight-bold'>Total</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type','donate_trust')->where('amount_status',1)->where('trust_id',$id)->sum('amount') }}</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type','donate_trust')->where('amount_status',1)->where('trust_id',$id)->sum('admin_commission') }}</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type','donate_trust')->where('amount_status',1)->where('trust_id',$id)->sum('final_amount') }}</th>
                        </fbody>
                        @endif
                    </table>
                </div>
            </div>
            <!-- Pagination for trust list -->
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-lg-end">
                    {!! $donate_trust_transaction->links() !!}
                </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($donate_trust_transaction) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
        </div>
    </div>


</div>