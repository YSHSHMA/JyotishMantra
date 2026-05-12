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
                                <input type="hidden" name='type' value='withdrowal_tran'>
                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Table displaying trust  -->
            @php
            $amount_withdrawal = \App\Models\WithdrawalAmountHistory::where('type', 'trust')->where('vendor_id', $trust_data['id'])->with(['Trust'])->orderBy('id', 'DESC')->paginate(10, ['*'], 'withdrowal_tran', request('withdrowal_tran', 1));
        
            @endphp
            <div class="text-start">
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('vendor_Info')}}</th>
                                <th>{{translate('amount')}}</th>
                                <th>{{translate('request_time')}}</th>
                                <th>{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                        </thead>
                        @if($amount_withdrawal)
                        <tbody>
                            @foreach($amount_withdrawal as $key => $withdrawRequest)
                            <tr>
                                <td>{{$loop->index }}</td>
                                <td>
                                    <div>
                                        <span>{{ $withdrawRequest['Trust']['name']??"" }}</span><br>
                                        <span>{{ $withdrawRequest['Trust']['trust_name']??"" }}</span><br>
                                        <span>{{ $withdrawRequest['Trust']['trust_email']??"" }}</span>
                                    </div>
                                </td>

                                <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($withdrawRequest['req_amount']??0) ), currencyCode: getCurrencyCode()) }}</td>
                                <td>
                                    <span>{{date("d M, Y h:i A", strtotime($withdrawRequest['created_at']??''))}}</span><br>
                                    @if($withdrawRequest['status'] == 1)
                                    <hr class="m-0">
                                    <span class="badge badge-soft-success">{{date("d M, Y h:i A", strtotime($withdrawRequest['updated_at']??''))}}</span>
                                    @elseif($withdrawRequest['status'] == 2)
                                    <hr class="m-0">
                                    <span class="badge badge-soft-danger">{{date("d M, Y h:i A", strtotime($withdrawRequest['updated_at']??''))}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($withdrawRequest['status'] == 0)
                                    <label class="badge badge-soft--primary">{{translate('pending')}}</label>
                                    @elseif($withdrawRequest['status'] == 1)
                                    <label class="badge badge-soft-success">{{translate('approved')}}</label>
                                    @elseif($withdrawRequest['status'] == 2)
                                    <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.donate_management.trustees-withdrawal.withdraw-request-view', [$withdrawRequest['id']]) }}" class="btn btn--primary btn-sm">
                                        <i class="tio-invisible"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <fbody>
                            <th colspan='5' class='font-weight-bold'>Approval</th>
                            <th class='font-weight-bold'>₹{{ \App\Models\WithdrawalAmountHistory::where('type','trust')->where('vendor_id',$id)->sum('req_amount') }}</th>
                        </fbody>
                        @endif
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