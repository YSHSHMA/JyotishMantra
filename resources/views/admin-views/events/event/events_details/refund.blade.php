<div class="col-md-12">
    <div class="w-100">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('seats_are_not_available') }}</h5>
        </div>
        <div class="card-body">
            <div class="px-3 py-4">
                <!-- Search bar -->
                @php
                $venue_data = (\App\Models\Events::where(['id'=>$getData['id']])->first()['all_venue_data']??"");
                @endphp
                <form action="{{ url()->current() }}" method="GET">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-md-12 col-lg-4 mb-2 mb-sm-0"> </div>
                        <div class="col-sm-4 col-md-6 col-lg-4 mb-2 mb-sm-0">
                            <select name="venue_id" class="form-control" onchange="this.form.submit()">
                                <option value="">{{translate('Select_Venue_option')}}</option>
                                @if(!empty($venue_data) && json_decode($venue_data))
                                @foreach(json_decode($venue_data) as $vl)
                                <option value="{{$vl->id}}" {{ (($vl->id == request('venue_id'))?'selected':'')}}>{{$vl->en_event_venue}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-4 text-right">
                            <div class="input-group input-group-custom input-group-merge">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                <input type="hidden" name="name" value='refund'>
                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Table displaying event organizers -->
            <div class="text-start">
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('order_id') }}</th>
                                <th>{{ translate('venue_name') }}</th>
                                <th>{{ translate('user_name') }}</th>
                                <th>{{ translate('TXT_ID') }}</th>
                                <th>{{ translate('RETXN_ID') }}</th>
                                <th>{{ translate('booking_date') }}</th>
                                <th>{{ translate('amount') }}</th>
                                <th>{{ translate('view') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through items -->
                            @if(!empty($order_refund_list))
                            @foreach($order_refund_list as $key => $items)
                            <?php
                            $venue_name = '';
                            if (!empty($items['eventid']['all_venue_data']) && json_decode($items['eventid']['all_venue_data'])) {
                                $venue_name = array_filter(json_decode($items['eventid']['all_venue_data']), function ($event) use ($items) {
                                    return ($event->id ?? "") == $items['venue_id'];
                                });
                                $venue_name = reset($venue_name);
                            } ?>
                            <tr>
                                <td>{{$order_refund_list->firstItem()+$key}}</td>
                                <td>{{ $items['order_no']}}</td>
                                <td><a title="{{ ($venue_name->event_venue??'') }}">{{ Str::limit(($venue_name->event_venue??''),20) }}</a></td>
                                <td>{{ $items['userdata']['name']}}</td>
                                <td>{{ $items['transaction_id']}} </td>
                                <td>{{ $items['refund_id']}} </td>
                                <td>{{ date("d M,Y H:i:s",strtotime($items['created_at']))}}</td>
                                <td>{{ $items['amount']}}₹ </td>
                                <td><a class='btn btn--primary btn-sm' onclick="orderitemviews(`{{ $items['id'] }}`)"><i class='tio-invisible'></i></a></td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <th>Total Order</th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',3)->where('venue_id',request('venue_id'))->count()}}
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',3)->count()}}
                                @endif
                            </th>
                            <th colspan='5'>Total Amount</th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',3)->where('venue_id',request('venue_id'))->sum('amount')}}₹
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',3)->sum('amount')}}₹
                                @endif
                            </th>
                            
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Pagination for event organizers list -->
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-lg-end">
                    {!! $order_refund_list->appends(request()->except('page'))->appends(['name' => 'refund'])->links() !!}
                </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($order_refund_list) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif


        </div>
    </div>
</div>