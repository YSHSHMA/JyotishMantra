<div class="col-md-12">
    <div class="w-100">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('order_info') }}</h5>
            
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
                                <input type="hidden" name="name" value='order'>
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
                                <th>{{ translate('booking_date') }}</th>
                                <th>{{ translate('Sub_amount') }}</th>
                                <th>{{ translate('Coupon_amount') }}</th>
                                <th>{{ translate('amount') }}</th>
                                <th>{{ translate('admin_commission') }}</th>
                                <th>{{ translate('GST_amount') }}</th>
                                <th>{{ translate('final_amount') }}</th>
                                <th>{{ translate('view') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through items -->
                            @if(!empty($order_list))
                            @foreach($order_list as $key => $items)
                            <?php
                            $venue_name = '';
                            if (!empty($items['eventid']['all_venue_data']) && json_decode($items['eventid']['all_venue_data'],true)) {
                                $venue_name = array_filter(json_decode($items['eventid']['all_venue_data'],true), function ($event) use ($items) {
                                    return ($event['id'] ?? "") == $items['venue_id'];
                                });
                                $venue_name = reset($venue_name);
                            } ?>
                            <tr>
                                <td>{{$order_list->firstItem()+$key}}</td>
                                <td>{{ $items['order_no']}}</td>
                                <td><a title="{{ ($venue_name['en_event_venue']??'') }}">{{ Str::limit(($venue_name['en_event_venue']??''),20) }}</a></td>
                                <td>{{ $items['userdata']['name']}}</td>
                                <td>{{ $items['transaction_id']}} </td>
                                <td>{{ date("d M,Y H:i:s",strtotime($items['created_at']))}}</td>
                                <td>{{ ($items['amount'] + ($items['coupon_amount']??0))}}₹ </td>
                                <td>{{ ($items['coupon_amount']??0)}}₹ </td>
                                <td>{{ $items['amount']}}₹ </td>
                                <td>{{ $items['admin_commission']}}₹</td>
                                <td>{{ $items['gst_amount']}}₹</td>
                                <td>{{ $items['final_amount']}}₹</td>
                                <td><a class='btn btn--primary btn-sm' onclick="orderitemviews(`{{ $items['id'] }}`)"><i class='tio-invisible'></i></a></td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <th>Total Order</th>

                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->count()}}
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->count()}}
                                @endif
                            </th>
                            <th colspan='4'>Total Amount</th>
                            <th>
                                @if(!empty(request('venue_id')))
                                @php
                                $sub_amount = \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('amount');
                                $coupon_amount = \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('coupon_amount');
                                @endphp
                                @else
                                @php
                                $sub_amount = \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('amount');
                                $coupon_amount = \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('coupon_amount');
                                @endphp
                                @endif

                                {{ ($coupon_amount + $sub_amount) }}₹
                            </th>
                            <th>
                                {{ $coupon_amount }}₹
                            </th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('amount')}}₹
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('amount')}}₹
                                @endif
                            </th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('admin_commission')}}₹
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('admin_commission')}}₹
                                @endif
                            </th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('gst_amount')}}₹
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('gst_amount')}}₹
                                @endif
                            </th>
                            <th>
                                @if(!empty(request('venue_id')))
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->where('venue_id',request('venue_id'))->sum('final_amount')}}₹
                                @else
                                {{ \App\Models\EventOrder::where('transaction_status',1)->where('event_id',$getData['id'])->where('status',1)->sum('final_amount')}}₹
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
                    {!! $order_list->appends(request()->except('page'))->appends(['name' => 'order'])->links() !!}
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
    </div>
</div>