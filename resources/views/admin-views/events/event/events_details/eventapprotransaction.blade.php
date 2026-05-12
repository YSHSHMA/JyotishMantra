<div class="card-body">
    <div class="text-start">
        <div class="table-responsive">
            <table id="datatable_transaction" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Uuid') }}</th>
                        <th>{{ translate('Type') }}</th>
                        <th>{{ translate('event_name') }}</th>
                        <th>{{ translate('transaction_id') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th>{{ translate('amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order_list as $key => $items)
                    <tr>
                        <td>{{ $key+1}}</td>
                        <td>{{ $items['uuid']}}</td>
                        <td>{{ (($items['types'] == 'event_approve')?"Approval":"Withdrawal")}}</td>
                        <td>{{ ($items['EventData']['event_name']??"NAN")}}</td>
                        <td>{{ $items['transaction_id']}}</td>
                        <td>{{ (($items['status'] == 1)?'Completed':(($items['status'] == 2)?'Failed':(($items['status'] == 3)?'Reject':'Pending'))) }}</td>
                        <td class="order_amount">{{ $items['amount']}}</td>   
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
            {!! $order_list->links() !!}
        </div>
    </div>
    <!-- Message for no data to show -->
    @if(count($order_list) == 0)
    <div class="text-center p-4">
        <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
    </div>
    @endif


</div>