@extends('layouts.back-end.app-event')

@section('title', translate('event_List'))

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('event_List') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class='row'>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('event-vendor.event-management.event-list')}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('All_Event')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            if(auth('event')->check()){
                            $relationEmployees = auth('event')->user()->relation_id;
                            }elseif(auth('event_employee')->check()){
                            $relationEmployees = auth('event_employee')->user()->relation_id;
                            }
                            echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('event-vendor.event-management.event-list',['is_approve'=>1])}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('approve')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('is_approve',1)->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('event-vendor.event-management.event-list',['is_approve'=>0])}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('not_Approve')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('is_approve',0)->count();
                            @endphp
                        </span>
                    </a>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
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
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Table displaying event event -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Event_ID') }}</th>
                                    <th>{{ translate('Event_order') }}</th>
                                    <th>{{ translate('Amount') }}</th>
                                    <th>{{ translate('Event_category') }}</th>
                                    <th>{{ translate('Event_name') }}</th>
                                    <th>{{ translate('Event_artist') }}</th>
                                    <th>{{ translate('venue') }}</th>
                                    <th>{{ translate('Event_Start_date_time') }}</th>
                                    <th>{{ translate('age_group') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('Verification_status') }}</th>
                                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Edit'))
                                    <th>{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through items -->
                                @foreach($getData as $key => $items)
                                <?php
                                $event_venue = '';
                                $dates = '';
                                $event_duration = '';
                                $last_dates = '';
                                $last_event_duration = '';
                                if (!empty($items['all_venue_data']) && json_decode($items['all_venue_data'])) {
                                    $allData = json_decode($items['all_venue_data']);
                                    $event_venue = $allData[0]->en_event_venue;
                                    $dates = date('d M,Y', strtotime($allData[0]->date)) . " " . $allData[0]->start_time;
                                    $event_duration = $allData[0]->event_duration;

                                    // Get the last value
                                    $lastItem = end($allData);
                                    $last_dates = date('d M,Y', strtotime($lastItem->date ?? '')) . " " . $lastItem->start_time ?? '';
                                    $last_event_duration = $lastItem->event_duration ?? "";
                                }
                                ?>
                                <tr>
                                    <td>{{$getData->firstItem()+$key}}</td>
                                    <td><a href="{{route('event-vendor.event-management.event-detail-overview',[$items['id']])}}" class='font-weight-bold text-secondary'>{{ $items['unique_id'] }}</a></td>
                                    <td><b>{{ count($items['EventOrder'])}}</b></td>
                                    <td><b>
                                            @php
                                            echo \App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->sum('amount');
                                            @endphp
                                        </b></td>
                                    <td>{{ ($items['categorys']['category_name']??'') }}</td>
                                    <td>{{ $items['event_name'] }}</td>
                                    <td>{{ ($items['eventArtist']['name']??'') }}</td>
                                    <td> <span data-placement="left" data-toggle="tooltip" title="{{ Str::limit(($event_venue??''),20) }}">{{ Str::limit(($event_venue??""),20) }}</span></td>
                                    <td>
                                        <span>{{ $dates??"" }}, {{ $event_duration??"" }}</span> <br>
                                        @if($dates != $last_dates)
                                        <span>{{ $last_dates??"" }}, {{ $last_event_duration??"" }}</span>
                                        @endif
                                    </td>

                                    <td> {{ ($items['age_group']??"") }}</td>
                                    <td>
                                        <span class="badge badge-pill ml-1 badge-soft-{{ $items['status'] == 1 ? 'success' : 'danger' }}"> {{ $items['status'] == 1 ? 'Active' : 'In-Active' }}</span>
                                    </td>
                                    <td>
                                        @if($items['is_approve'] == 1)
                                        Approve
                                        @elseif($items['is_approve'] == 2)
                                        Send Request
                                        @elseif($items['is_approve'] == 3)
                                        Reject
                                        @elseif($items['is_approve'] == 4)
                                        Pending(Resend)
                                        @else
                                        Pending
                                        @endif
                                    </td>
                                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Edit'))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('event-vendor.event-management.event-update',[$items['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination for event event list -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {!! $getData->links() !!}
                    </div>
                </div>
                <!-- Message for no data to show -->
                @if(count($getData) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-events-delete" data-url="{{ route('admin.event-managment.event.delete') }}"></span>
<!-- Toast message for event event deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            {{ translate('events_deleted') }}
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    "use strict";
    // Retrieve localized texts
    let getYesWord = $('#message-yes-word').data('text');
    let getCancelWord = $('#message-cancel-word').data('text');
    let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
    let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');

    // Handle delete button click
    $('.events-delete-button').on('click', function() {
        let EventsId = $(this).attr("id");
        Swal.fire({
            title: messageAreYouSureDeleteThis,
            text: messageYouWillNotAbleRevertThis,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: getYesWord,
            cancelButtonText: getCancelWord,
            icon: 'warning',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                // Send AJAX request to delete event caregory
                $.ajax({
                    url: $('#route-admin-events-delete').data('url'),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: EventsId
                    },
                    success: function(response) {
                        // Show success message
                        toastr.success('event deleted successfully', '', {
                            positionClass: 'toast-bottom-left'
                        });
                        // Reload the page
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
        });
    });
</script>
@endpush