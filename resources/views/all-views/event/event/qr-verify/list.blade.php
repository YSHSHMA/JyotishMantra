@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-event')

@section('title', translate('event_booking'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('event_booking') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 form-group">
                            <!-- trust-tab -->
                            <div class="px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-6 col-md-6 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-custom input-group-merge">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_Event_name') }}" aria-label="{{ translate('search_by_Event_name') }}" required>
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
                                                <th>{{ translate('event_name') }} </th>
                                                <th>{{ translate('venue_info') }} </th>
                                                <th>{{ translate('Total_booking') }}</th>
                                                <th>{{ translate('amount_info') }}</th>
                                                <th>{{ translate('attendees') }}</th>
                                                <th>{{ translate('action') }}</th>
                                            </tr>
                                        </thead>
                                        @if(!empty($getevent) && count($getevent) > 0)
                                        <tbody>
                                            @foreach($getevent as $key => $items)
                                            <tr>
                                                <td>{{$getevent->firstItem()+$key}}</td>
                                                <td><a href="{{route('event-vendor.event-management.event-detail-overview',[$items['id']])}}" class='font-weight-bold text-secondary'>{{ ($items['unique_id']??"") }}</a></td>
                                                <td><span role='tooltip' data-toggle="tooltip" data-placement="left" title="{{ ($items['event_name']??'') }}">{{ Str::Limit(($items['event_name']??''),30) }}</span></td>
                                                <td>
                                                    @php
                                                    $venueData = json_decode($items['all_venue_data'], true);
                                                    $todayVenue = collect($venueData)->firstWhere('date', date('Y-m-d'));
                                                    @endphp
                                                    <div>
                                                        <strong>Venue:</strong> <span role='tooltip' data-toggle="tooltip" data-placement="left" data-title="{{ $todayVenue['en_event_venue'] ?? 'N/A' }}">{{ Str::limit(($todayVenue['en_event_venue'] ?? 'N/A'),25) }}</span><br>
                                                        <strong>Date:</strong> {{ $todayVenue['date'] ?? 'N/A' }} <br>
                                                        <strong>start Time:</strong> {{ $todayVenue['start_time'] ?? 'N/A' }} <br>
                                                        <strong>End Time:</strong> {{ $todayVenue['end_time'] ?? 'N/A' }} <br>
                                                    </div>
                                                </td>
                                                <td>{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->count()) }}</td>
                                                <td>
                                                    <span>Total&nbsp;:&nbsp;{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->sum('amount')) }}</span><br>
                                                    <span>Coupon&nbsp;:&nbsp;{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->sum('coupon_amount')) }}</span><br>
                                                    <span>admin&nbsp;:&nbsp;{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->sum('admin_commission')) }}</span><br>
                                                    <span>GST&nbsp;:&nbsp;{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->sum('gst_amount')) }}</span><br>
                                                    <span>Final&nbsp;:&nbsp;{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->sum('final_amount')) }}</span><br>
                                                </td>
                                                <td>{{ (\App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->where('venue_id',$todayVenue['id'])->with('orderitem')->get()
                                                    ->flatMap(function ($order) {
                                                        return collect(json_decode($order->orderitem[0]['user_information'] ?? '[]', true));
                                                    })
                                                    ->where('verify', 1)
                                                    ->count()) }}
                                                </td>
                                                <td>
                                                    @if (Helpers::Employee_modules_permission('Qr Management', 'Qr Verify', 'Scan'))
                                                    <a href="{{ route('event-vendor.qr-code-verify.view',['id'=>$items['id'],'venue'=>$todayVenue['id']]) }}" class="btn btn-outline-info btn-sm px-1 py-1" style="font-size: 26px;"><i class="tio-qr_code nav-icon">qr_code</i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {!! $getevent->links() !!}
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


@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>

</script>
@endpush