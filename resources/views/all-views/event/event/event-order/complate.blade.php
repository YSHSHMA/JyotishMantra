@extends('layouts.back-end.app-event')
@section('title', translate('event_booking_complate'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('event_booking_complate') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item text-capitalize">
                            <a class="nav-link form-system-language-tab cursor-pointer {{ ((empty(request('show')) || request('show') == 'all')?'active':'')}}" id="Allrecode-link">All Booking</a>
                        </li>
                        <li class="nav-item text-capitalize">
                            <a class="nav-link form-system-language-tab cursor-pointer {{ ((!empty(request('show')) && request('show') == 'event')?'active':'')}}" id="trust-link">Event</a>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-12 form-group form-system-language-form {{ ((empty(request('show')) || request('show') == 'all')?'':'d-none')}}" id="Allrecode-form">
                            <div class="px-3 py-4">
                                <!-- Search bar -->
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='show' value="all">
                                        </form>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-4"></div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='show' value="all">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('Order_ID') }}</th>
                                                <th>{{ translate('customer_info') }}</th>
                                                <th>{{ translate('event') }} Info </th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('coupon_amount') }}</th>
                                                <th>{{ translate('admin_commission') }}</th>
                                                <th>{{ translate('gst_amount') }}</th>
                                                <th>{{ translate('final_amount') }}</th>
                                                <th>{{ translate('option') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($getOrder as $key => $items)
                                            <tr>
                                                <td>{{$getOrder->firstItem()+$key}}</td>
                                                <td>{{ ($items['order_no']??"") }}</td>
                                                <td>
                                                    <span>{{ ($items['userdata']['name']??"") }}</span><br>
                                                    <span>{{ ($items['userdata']['phone']??"") }}</span><br>
                                                    <span>{{ date('d M,Y H:i:s',strtotime($items['created_at']??"")) }}</span>
                                                </td>
                                                <td>
                                                    <span data-toggle="tooltip" data-title="{{ ($items['eventid']['event_name']??'') }}" role='tooltip'>{{ Str::limit(($items['eventid']['event_name']??''),20) }}</span><br>
                                                    <span>{{ ($items['eventid']['categorys']['category_name']??'') }}</span><br>
                                                    <?php
                                                    $venue_name = '';
                                                    $create_date = '';
                                                    if (!empty($items['eventid']['all_venue_data']) && json_decode($items['eventid']['all_venue_data'], true)) {
                                                        $venue_name = array_filter(json_decode($items['eventid']['all_venue_data'], true), function ($event) use ($items) {
                                                            return ($event['id'] ?? "") == $items['venue_id'];
                                                        });
                                                        $venue_name = reset($venue_name);
                                                    } ?>
                                                    <span data-toggle="tooltip" data-title="{{ ($venue_name['en_event_venue']??'') }}" role='tooltip'>{{ Str::limit(($venue_name['en_event_venue']??""),20) }}</span><br>
                                                    <span>{{ ($items['eventid']['organizer_by']??'') }}</span>
                                                    <br>
                                                    <span>{{ ($venue_name['date']??"") }}</span>
                                                </td>
                                                <td>{{ (($items['amount']??'0') + ($items['coupon_amount']??'0') ) }}</td>
                                                <td>{{ ($items['coupon_amount']??'0') }}</td>
                                                <td>{{ ($items['admin_commission']??'0') }}</td>
                                                <td>{{ ($items['gst_amount']??'0') }}</td>
                                                <td>{{ ($items['final_amount']??'0') }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}" onclick="orderitemviews(`{{$items['id']}}`)">
                                                            <i class="tio-visible_outlined">visible_outlined</i>
                                                        </a>
                                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}">
                                                            <i class="tio-share_vs">share_vs</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @if($getOrder && count($getOrder) > 0)
                                        <fbody>
                                            <tr>
                                                <th class="font-weight-bold" colspan="4">Total Amount</th>
                                                <th class="font-weight-bold">{{ (($order_array['amount']??0) + ($order_array['coupon_amount']??0)) }}</th>
                                                <th class="font-weight-bold">{{ ($order_array['coupon_amount']??0) }}</th>
                                                <th class="font-weight-bold">{{ ($order_array['admin_commission']??0) }}</th>
                                                <th class="font-weight-bold">{{ ($order_array['gst_amount']??0) }}</th>
                                                <th class="font-weight-bold" colspan="2">{{ ($order_array['final_amount']??0) }}</th>
                                            </tr>
                                        </fbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getOrder->appends(['show' => 'all'])->links() !!}

                                </div>
                            </div>
                            @if(count($getOrder) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-12 form-group form-system-language-form {{ ((!empty(request('show')) && request('show') == 'event')?'':'d-none')}}" id="trust-form">
                            <!-- trust-tab -->
                            <div class="px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='show' value="event">
                                        </form>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-4"></div>
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='show' value="event">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('ID') }}</th>
                                                <th>{{ translate('Total_booking') }}</th>
                                                <th>{{ translate('event_name') }} </th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('coupon_amount') }}</th>
                                                <th>{{ translate('admin_commission') }}</th>
                                                <th>{{ translate('GST_amount') }}</th>
                                                <th>{{ translate('Total_amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($getevent as $key => $items)
                                            <tr>
                                                <td>{{$getevent->firstItem()+$key}}</td>
                                                <td><a href="{{route('event-vendor.event-management.event-detail-overview',[$items['event_id']])}}" class='font-weight-bold text-secondary'>{{ ($items['eventid']['unique_id']??"") }}</a></td>
                                                <td>{{ ($items['total_orders']??"")}}</td>
                                                <td><span role='tooltip' data-toggle="tooltip" title="{{ ($items['eventid']['event_name']??'') }}">{{ Str::Limit(($items['eventid']['event_name']??''),30) }}</span></td>
                                                <td>{{ (($items['amount']??'') + ($items['coupon_amount']??'')) }}</td>
                                                <td>{{ ($items['coupon_amount']??'') }}</td>
                                                <td>{{ ($items['admin_commission']??'') }}</td>
                                                <td>{{ ($items['gst_amount']??'') }}</td>
                                                <td>{{ ($items['final_amount']??'') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @if($getevent && count($getevent) > 0)
                                        <fbody>
                                            <tr>
                                                <th class="font-weight-bold" colspan="4">Total Amount</th>
                                                <th class="font-weight-bold">{{ (($event_array['amount']??0) + ($event_array['coupon_amount']??0)) }}</th>
                                                <th class="font-weight-bold">{{ ($event_array['coupon_amount']??0) }}</th>
                                                <th class="font-weight-bold">{{ ($event_array['admin_commission']??0) }}</th>
                                                <th class="font-weight-bold">{{ ($event_array['gst_amount']??0) }}</th>
                                                <th class="font-weight-bold" colspan="2">{{ ($event_array['final_amount']??0) }}</th>
                                            </tr>
                                        </fbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getevent->appends(['show' => 'event'])->links() !!}
                                </div>
                            </div>
                            @if(count($getevent) == 0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="modal_order_view" class="modal fade modal-center modal-order" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><i class="icon-close" aria-hidden="true"></i></button>
                <h4 class="modal-title">Order view</h4>
                <div class="form-group view_orders_items">

                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    function initializeDateRangePicker(isSingleDate) {
        $('.start_date_end_date').daterangepicker({
            singleDatePicker: isSingleDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            // When a date range is selected, set the min and max dates on the individual date pickers
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: start.format('YYYY-MM-DD'),
                maxDate: end.format('YYYY-MM-DD')
            });
        });
    }

    // Initial setup for date range picker
    initializeDateRangePicker(false);

    function orderitemviews(order_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('event-vendor.event-order.event-order-view')}}",
            data: {
                order_id
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                if (data.success == 1) {
                    $('#modal_order_view').modal('show');
                    $(".view_orders_items").html(data.data);

                } else {
                    toastr.error('Data Not Found');
                }

            }
        });
    }
</script>
@endpush