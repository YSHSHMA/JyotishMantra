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
                                <th>{{ translate('user_info') }}</th>
                                <th>{{ translate('tour_info') }}</th>
                                <th>{{ translate('TXT_ID') }}</th>
                                <th>{{ translate('Sub_amount') }}</th>
                                <th>{{ translate('Coupon_amount') }}</th>
                                <th>{{ translate('amount') }}</th>
                                <th>{{ translate('admin_commission') }}</th>
                                <th>{{ translate('GST_amount') }}</th>
                                <th>{{ translate('final_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through items -->
                            @if(!empty($refund_list))
                            @foreach($refund_list as $key => $items)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span>{{ $items['userData']['name']}}</span><br>
                                    <span>{{ $items['userData']['phone']}}</span><br>
                                    <span>qty: {{ $items['qty'] }}</span><br>
                                    <span>package :
                                        @if(!empty($getData['package_list']) && json_decode($getData['package_list'],true))
                                        @foreach(json_decode($getData['package_list'],true) as $val)
                                        @if($val['id'] == $items['package_id'])
                                        {{ (\App\Models\TourCab::where('id',$val['cab_id'])->first()['name']??"") }}
                                        <a role='tooltip' data-toggle="tooltip" data-html="true" title="
                                        @if(!empty($val['package_id']??''))
                                        @foreach($val['package_id'] as $pn)
                                        <p>Package added : <strong>{{ (\App\Models\TourPackage::where('id',($pn??''))->first()['name']??'') }}</strong></p>
                                        @endforeach 
                                        @endif
                                        ">
                                        <i class="tio-info"></i>
                                    </a>
                                        @endif
                                        @endforeach
                                        @endif
                                    </span><br>
                                    <span>{{ date("d M,Y h:i A",strtotime($items['created_at']))}}</span>
                                </td>
                                <td>
                                    @if($items['company'])
                                    <span> {{ $items['company']['owner_name']??"" }} </span><br>
                                    <span> {{ Str::limit(($items['company']['company_name']??""), 20) }} </span><br>
                                    <span> {{ $items['company']['phone_no']??"" }}</span><br>
                                    @else
                                    <span class="btn btn-outline-danger btn-sm mb-1">Cab Not assigned</span><br>
                                    @endif
                                    <span> {{ date("d M,Y",strtotime($items['pickup_date']??"")) }} {{ $items['pickup_time']??"" }}</span>
                                </td>
                                <td>{{ $items['transaction_id']}} </td>
                                <td>{{ ($items['amount'] + ($items['coupon_amount']??0))}}₹ </td>
                                <td>{{ ($items['coupon_amount']??0)}}₹ </td>
                                <td>{{ $items['amount']}}₹ </td>
                                <td>{{ $items['admin_commission']}}₹</td>
                                <td>{{ $items['gst_amount']}}₹</td>
                                <td>{{ $items['final_amount']}}₹</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <th>Total Order</th>
                            <th>
                                @if(!empty(request('cab_assign')))
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->count()}}
                                @else
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->count()}}
                                @endif
                            </th>
                            <th colspan='2'>Total Amount</th>
                            <th>
                                @php
                                if(!empty(request('cab_assign'))){
                                $sub_amount = (\App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->sum('amount'));
                                (\App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->sum('coupon_amount'));
                                }else{
                                $sub_amount = (\App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->sum('amount'));
                                $coupon_amount = (\App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->sum('coupon_amount'));
                                }
                                @endphp
                                {{ ($coupon_amount + $sub_amount) }}₹
                            </th>
                            <th>
                                {{ $coupon_amount }}₹
                            </th>
                            <th>
                                {{ $sub_amount }}₹

                            </th>
                            <th>
                                @if(!empty(request('cab_assign')))
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->sum('admin_commission')}}₹
                                @else
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->sum('admin_commission')}}₹
                                @endif
                            </th>
                            <th>
                                @if(!empty(request('cab_assign')))
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->sum('gst_amount')}}₹
                                @else
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->sum('gst_amount')}}₹
                                @endif
                            </th>
                            <th>
                                @if(!empty(request('cab_assign')))
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->where('cab_assign',request('cab_assign'))->sum('final_amount')}}₹
                                @else
                                {{ \App\Models\TourOrder::where('amount_status',1)->where('tour_id',$getData['id'])->where('status','!=',2)->sum('final_amount')}}₹
                                @endif
                            </th>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Pagination for event organizers list -->
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-lg-end">
                {!! $refund_list->appends(request()->except('page1'))->appends(['name' => 'order'])->links('pagination::bootstrap-4') !!}
                </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($refund_list) == 0)
            <div class="text-center p-4">
                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif


        </div>
    </div>
</div>