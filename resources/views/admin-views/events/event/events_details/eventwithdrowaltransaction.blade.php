<div class="card-body">
    <div class="text-start">
        <div class="table-responsive">
            <table id="datatable_transaction" class="table ">
                <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('current_balance') }}</th>
                        <th>{{ translate('req_amount') }}</th>
                        <th>{{ translate('approval_amount') }}</th>
                        <th>{{ translate('pay_status') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('option') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrowalTransaction as $key => $items)
                    <tr>
                        <td>{{ $key+1}}</td>
                        <td>{{ $items['old_wallet_amount']??''}}</td>
                        <td>{{ ($items['req_amount']??"")}}</td>
                        <td>{{ ($items['approval_amount']??"")}}</td>
                        <td>
                            @if($items['status'] == 1)
                            Id:<span class="font-weight-bolder">&nbsp;{{ $items['transcation_id']}}</span><br>
                            Method:<span class="font-weight-bolder">&nbsp; {{ $items['payment_method']}}</span><br>
                            @endif
                            <span class="btn btn-outline-{{ (($items['status'] == 1)?'success':(($items['status'] == 2)?'danger':(($items['status'] == 3)?'danger':'warning'))) }}">{{ (($items['status'] == 1)?'Completed':(($items['status'] == 2)?'Failed':(($items['status'] == 3)?'Reject':'Pending'))) }}</span>
                        </td>
                        <td>
                            <span>Requested Date</span>: <span class="font-weight-bolder">{{ date("d M,Y h:i A",strtotime(($items['created_at']??'')))}}</span><br>
                            @if($items['status'] == 1)
                            <span>Approved Date</span>: <span class="font-weight-bolder">{{ date("d M,Y h:i A",strtotime(($items['updated_at']??'')))}}</span><br>
                            @elseif($items['status'] == 1)
                            <span>Rejected Date</span>: <span class="font-weight-bolder">{{ date("d M,Y h:i A",strtotime(($items['updated_at']??'')))}}</span><br>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm btn-outline-info" onclick="$('.modelopen_{{$key}}').modal()"><i class="tio-invisible"></i></a><br>
                            <div class="modal modelopen_{{$key}}" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Information</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mt-2">
                                                <div class="col-md-6 form-group">
                                                    <label>Holder Name</label>
                                                    <label class="font-weight-bolder">:&nbsp;{{ $items['holder_name']}}</label>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Bank Name</label>
                                                    <label class="font-weight-bolder">:&nbsp;{{ $items['bank_name']}}</label>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>IFSC Code</label>
                                                    <label class="font-weight-bolder">:&nbsp;{{ $items['ifsc_code']}}</label>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Account Number</label>
                                                    <label class="font-weight-bolder">:&nbsp;{{ $items['account_number']}}</label>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <hr>
                                                    <label class="font-weight-bolder">Or</label>
                                                    <hr>
                                                </div>

                                                <div class="col-md-12 form-group">
                                                    <label>URI</label>
                                                    <label class="font-weight-bolder">:&nbsp;{{ $items['upi_code']}}</label>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total</th>
                        <th></th>
                        <th id="totalOrderAmount"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- Pagination for event organizers list -->
    <div class="table-responsive mt-4">
        <div class="d-flex justify-content-lg-end">
            {!! $withdrowalTransaction->links() !!}
        </div>
    </div>
    <!-- Message for no data to show -->
    @if(count($withdrowalTransaction) == 0)
    <div class="text-center p-4">
        <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
    </div>
    @endif


</div>