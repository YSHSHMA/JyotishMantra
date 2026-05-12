@extends('layouts.back-end.app')

@section('title', translate('event_'.request()->segment(4)))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('event_'.request()->segment(4)) }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class='row'>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4))}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('All_Event')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            if(request()->segment(4) == 'pending'){
                            echo ((\App\Models\Events::whereIn('is_approve',[0,2,3,4])->whereIn('status',[0,1])->count()??0) + (\App\Models\Events::where('is_approve',1)->where('status',0)->count()??0));
                            }elseif(request()->segment(4) == 'booking'){
                            echo \App\Models\Events::where('is_approve', 1)
                            ->where('status', 1)
                            ->where(function ($query) {
                            $query->whereRaw("
                            DATE(?) BETWEEN
                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d')
                            AND
                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                            ", [now()->format('Y-m-d')])
                            ->orWhereRaw("
                            DATE(?) =
                            STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                            ", [now()->format('Y-m-d')]);
                            })
                            ->count();

                            }elseif(request()->segment(4) == 'upcomming'){
                            echo \App\Models\Events::where('is_approve', 1)
                            ->where('status', 1)
                            ->whereRaw("
                            DATE(?) < STR_TO_DATE(
                                IF(INSTR(start_to_end_date, ' - ' )> 0,
                                SUBSTRING_INDEX(start_to_end_date, ' - ', 1),
                                start_to_end_date
                                ), '%Y-%m-%d')
                                ", [now()->format('Y-m-d')])
                                ->count();
                                }elseif(request()->segment(4) == 'completed'){

                                echo \App\Models\Events::where('is_approve', 1)
                                ->where('status', 1)
                                ->whereRaw("
                                DATE(?) > STR_TO_DATE(
                                IF(INSTR(start_to_end_date, ' - ') > 0,
                                SUBSTRING_INDEX(start_to_end_date, ' - ', -1),
                                start_to_end_date
                                ), '%Y-%m-%d')
                                ", [now()->format('Y-m-d')])
                                ->count();

                                }elseif(request()->segment(4) == 'canceled'){
                                echo \App\Models\Events::where('status',0)->count();
                                }else{
                                echo '0';
                                }
                                @endphp
                        </span>
                    </a>
                </div>
                <div class=" col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['organizer'=>'inhouse'])}}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('Inhouse')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            if(request()->segment(4) == 'pending'){
                            echo ((\App\Models\Events::where('organizer_by','inhouse')->whereIn('is_approve',[0,2,3,4])->whereIn('status',[0,1])->count()??0) + (\App\Models\Events::where('organizer_by','inhouse')->where('is_approve',1)->where('status',0)->count()??0) );
                            }elseif(request()->segment(4) == 'booking'){
                            echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','inhouse')
                            ->where('status', 1)
                            ->where(function ($query) {
                            $query->whereRaw("
                            DATE(?) BETWEEN
                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d')
                            AND
                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                            ", [now()->format('Y-m-d')])
                            ->orWhereRaw("
                            DATE(?) =
                            STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                            ", [now()->format('Y-m-d')]);
                            })
                            ->count();
                            }elseif(request()->segment(4) == 'upcomming'){
                            echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','inhouse')
                            ->where('status', 1)
                            ->whereRaw("
                            DATE(?) < STR_TO_DATE(
                                IF(INSTR(start_to_end_date, ' - ' )> 0,
                                SUBSTRING_INDEX(start_to_end_date, ' - ', 1),
                                start_to_end_date
                                ), '%Y-%m-%d')
                                ", [now()->format('Y-m-d')])
                                ->count();

                                }elseif(request()->segment(4) == 'completed'){                                
                                echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','inhouse')
                                ->where('status', 1)
                                ->whereRaw("
                                DATE(?) > STR_TO_DATE(
                                IF(INSTR(start_to_end_date, ' - ') > 0,
                                SUBSTRING_INDEX(start_to_end_date, ' - ', -1),
                                start_to_end_date
                                ), '%Y-%m-%d')
                                ", [now()->format('Y-m-d')])
                                ->count();
                                                
                                                }elseif(request()->segment(4) == 'canceled'){
                                echo \App\Models\Events::where('organizer_by','inhouse')->where('status',0)->count();
                                }else{
                                echo '0';
                                }
                                @endphp
                        </span>
                    </a>
                </div>
                <div class=" col-sm-6 col-lg-3 col-md-3 mt-2">
                                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['organizer'=>'outside'])}}">
                                        <div class="order-stats__content">
                                            <i class="tio-all_done">all_done</i>
                                            <h6 class="order-stats__subtitle">{{ translate('Outsite')}}</h6>
                                        </div>
                                        <span class="order-stats__title">
                                            @php
                                            if(request()->segment(4) == 'pending'){
                                            echo ((\App\Models\Events::where('organizer_by','outside')->whereIn('is_approve',[0,2,3,4])->whereIn('status',[0,1])->count()??0) + (\App\Models\Events::where('organizer_by','outside')->where('is_approve',1)->where('status',0)->count()??0));
                                            }elseif(request()->segment(4) == 'booking'){
                                            echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','outside')
                                            ->where('status', 1)
                                            ->where(function ($query) {
                                            $query->whereRaw("
                                            DATE(?) BETWEEN
                                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d')
                                            AND
                                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                                            ", [now()->format('Y-m-d')])
                                            ->orWhereRaw("
                                            DATE(?) =
                                            STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                                            ", [now()->format('Y-m-d')]);
                                            })
                                            ->count();

                                            }elseif(request()->segment(4) == 'upcomming'){
                                            echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','outside')
                                            ->where('status', 1)
                                            ->whereRaw("
                                            DATE(?) < STR_TO_DATE(
                                                IF(INSTR(start_to_end_date, ' - ' )> 0,
                                                SUBSTRING_INDEX(start_to_end_date, ' - ', 1),
                                                start_to_end_date
                                                ), '%Y-%m-%d')
                                                ", [now()->format('Y-m-d')])
                                                ->count();
                                                }elseif(request()->segment(4) == 'canceled'){
                                                echo \App\Models\Events::where('organizer_by','outside')->where('status',0)->count();
                                                }elseif(request()->segment(4) == 'completed'){                                                
                                echo \App\Models\Events::where('is_approve', 1)->where('organizer_by','outside')
                                ->where('status', 1)
                                ->whereRaw("
                                DATE(?) > STR_TO_DATE(
                                IF(INSTR(start_to_end_date, ' - ') > 0,
                                SUBSTRING_INDEX(start_to_end_date, ' - ', -1),
                                start_to_end_date
                                ), '%Y-%m-%d')
                                ", [now()->format('Y-m-d')])
                                ->count();
                                
                            }else{
                                echo '0';
                                }
                                @endphp
                        </span>
                    </a>
                </div>

                @php
                if(request()->segment(4) == 'pending'){
                @endphp
                <div class=" col-sm-6 col-lg-3 col-md-3 mt-2">
                                                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['is_approve'=>'1'])}}">
                                                        <div class="order-stats__content">
                                                            <i class="tio-remaining_time"></i>
                                                            <h6 class="order-stats__subtitle">{{ translate('Approved')}}</h6>
                                                        </div>
                                                        <span class="order-stats__title">
                                                            @php
                                                            echo \App\Models\Events::where('is_approve',1)->where('status',0)->count();
                                                            @endphp
                                                        </span>
                                                    </a>
                </div>
                <div class=" col-sm-6 col-lg-3 col-md-3 mt-2">
                                                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['is_approve'=>'4'])}}">
                                                        <div class="order-stats__content">
                                                            <i class="tio-remaining_time"></i>
                                                            <h6 class="order-stats__subtitle">{{ translate('Request_Failed')}}</h6>
                                                        </div>
                                                        <span class="order-stats__title">
                                                            @php
                                                            echo \App\Models\Events::where('is_approve',4)->where('status',1)->count();
                                                            @endphp
                                                        </span>
                                                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['is_approve'=>'0'])}}">
                        <div class="order-stats__content">
                            <i class="tio-remaining_time"></i>
                            <h6 class="order-stats__subtitle">{{ translate('Pending')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\Events::where('is_approve',0)->whereIn('status',[0,1])->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['is_approve'=>'2'])}}">
                        <div class="order-stats__content">
                            <i class="tio-remaining_time"></i>
                            <h6 class="order-stats__subtitle">{{ translate('Request_Pending')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\Events::where('is_approve',2)->whereIn('status',[0,1])->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('admin.event-managment.event.'.request()->segment(4),['is_approve'=>'3'])}}">
                        <div class="order-stats__content">
                            <i class="tio-remaining_time"></i>
                            <h6 class="order-stats__subtitle">{{ translate('Reject')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\Events::where('is_approve',3)->whereIn('status',[0,1])->count();
                            @endphp
                        </span>
                    </a>
                </div>
                @php
                }
                @endphp
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
                                    <th>{{ translate('Organizer_name') }}</th>
                                    <th>{{ translate('Event_name') }}</th>
                                    <th>{{ translate('Event_artist') }}</th>
                                    <th>{{ translate('venue') }}</th>
                                    <th>{{ translate('Event_Start_date_time') }}</th>
                                    <th>{{ translate('Event_duration') }}</th>
                                    <th>{{ translate('age_group') }}</th>
                                    <th>{{ translate('organize_type') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('Verification_status') }}</th>
                                    <th>{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through items -->
                                @foreach($getData as $key => $items)
                                <?php
                                $event_venue = '';
                                $dates = '';
                                $event_duration = '';

                                if (!empty($items['all_venue_data']) && json_decode($items['all_venue_data'])) {
                                    $allData = json_decode($items['all_venue_data']);

                                    $event_venue = $allData[0]->en_event_venue;
                                    $dates = date('d M,Y', strtotime($allData[0]->date)) . " " . $allData[0]->start_time;
                                    $event_duration = $allData[0]->event_duration;
                                }
                                ?>
                                <tr>
                                    <td>{{$getData->firstItem()+$key}}</td>
                                    <td><a href="{{route('admin.event-managment.event.event-detail-overview',[$items['id']])}}" class='font-weight-bold text-secondary'>{{ $items['unique_id'] }}</a></td>
                                    <td><b>{{ \App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->count() }}</b></td>
                                    <td><b>
                                            @php
                                            echo \App\Models\EventOrder::where('event_id',$items['id'])->where('transaction_status',1)->where('status',1)->sum('amount');
                                            @endphp
                                        </b></td>
                                    <td>{{ ($items['categorys']['category_name']??'') }}</td>
                                    <td>{{ ($items['organizers']['organizer_name']??'') }}</td>
                                    <td>{{ $items['event_name'] }}</td>
                                    <td>{{ ($items['eventArtist']['name']??'') }}</td>
                                    <td>{{ Str::limit(($event_venue??""),20) }}</td>
                                    <td>{{ $dates??"" }}</td>
                                    <td>{{ $event_duration??"" }} </td>
                                    <td> {{ ($items['age_group']??"") }}</td>
                                    <td> {{ ($items['organizer_by']??"") }}</td>
                                    <td>
                                        <form action="{{route('admin.event-managment.event.status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$items['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="items-status{{ $items['id'] }}" value="1" {{ $items['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="items-status{{ $items['id'] }}" data-on-image="items-status-on.png" data-off-image="items-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' Event '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' Event '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_events_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_event_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        @if($items['is_approve'] == 1)
                                        Approve
                                        @elseif($items['is_approve'] == 2)
                                        Send Request
                                        @elseif($items['is_approve'] == 3)
                                        Reject
                                        @elseif($items['is_approve'] == 4)
                                        <a href="{{ route('admin.event-managment.event.event_approvel',[$items['id'],2,'amount'=>$items['event_approve_amount']]) }}" class='btn btn-warning text-white btn-sm'>Resend</a>
                                        @else
                                        Pending
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-sm btn-outline-success" href="{{route('admin.event-managment.event.information',[$items['id']])}}"><i class="tio-invisible"></i></a>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.event-managment.event.update',[$items['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="events-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @if($items['is_approve'] == 1 && $items['status'] == 0)
                                            <a onclick="return confirm('are you sure!')" class="btn btn-sm btn-outline-info" href="{{route('admin.event-managment.event.refund-amount',[$items['id']])}}" title="event cancel and user refund" role="tooltip" data-toggle="tooltip"><i class="tio-replay"></i></a>
                                        @endif
                                        </div>
                                    </td>
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