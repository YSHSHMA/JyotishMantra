@php use Carbon\Carbon; @endphp
@extends('layouts.back-end.app')

@section('title', translate('support_Ticket'))

@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-8">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/support_ticket.png') }}"
                    alt="">
                {{ translate('support_ticket') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $getData->total() }}</span>
            </h2>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed" href="{{ route('admin.support-ticket.view')}}">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('All_query')}}</h6>
                </div>
                <span class="order-stats__title"> {{ \App\Models\VendorSupportTicketConv::where('created_by','vendor')->whereNotNull('ticket_id')->count()}} </span>
            </a>
        </div>
        @if($TypeList)
        @foreach($TypeList as $v_type)
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ ucwords($v_type ??'')}}</h6>
                </div>
                <span class="order-stats__title">{{ \App\Models\VendorSupportTicketConv::where('created_by','vendor')->where('type',$v_type)->where('status','open')->count()}} /
                    <span class="order-stats__title text-danger">{{ \App\Models\VendorSupportTicketConv::where('created_by','vendor')->where('type',$v_type)->where('status','close')->count()}} </span>
                </span>
            </a>
        </div>
        @endforeach
        @endif
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="">
                <div class="px-3 py-4 mb-3 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                        <div class="">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-merge input-group-custom">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_ticket_by_subject_or_status') . '...' }}"
                                        aria-label="Search orders" value="{{ request('searchValue') }}">
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="">
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-3 justify-content-end">
                                @php($priority = request()->has('type') ? request()->input('type') : '')
                                <select class="form-control border-color-c1 w-160 filter-tickets" data-value="type">
                                    <option value="all">{{ translate('all_Type') }}</option>
                                    @if($TypeList)
                                    @foreach($TypeList as $v_data)
                                    <option value="{{ $v_data}}" {{ $priority == $v_data ? 'selected' : '' }}>{{ $v_data}}</option>
                                    @endforeach
                                    @endif
                                </select>

                                @php($status = request()->has('status') ? request()->input('status') : '')
                                <select class="form-control border-color-c1 w-160 filter-tickets" data-value="status">
                                    <option value="all">{{ translate('all_Status') }}</option>
                                    <option value="open" {{ $status == 'open' ? 'selected' : '' }}>{{ translate('open') }}
                                    </option>
                                    <option value="close" {{ $status == 'close' ? 'selected' : '' }}>
                                        {{ translate('close') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach ($getData->reverse() as $key => $ticket)
                <?php
                $getCountunread = \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $ticket['id'])->where('read_admin_status', 0)->count();
                ?>
                <div class="border-bottom mb-3 pb-3">
                    <div class="card">
                        <div class="card-body align-items-center d-flex flex-wrap justify-content-between gap-3 border-bottom">
                            <div class="media gap-3">
                                @if($ticket)
                                @if($ticket['Tour'] && $ticket['type'] == 'tour')
                                <img class="avatar avatar-lg" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $ticket['Tour']['image'] ?? '', type: 'backend-profile') }}" alt="">
                                @elseif($ticket['seller'] && $ticket['type'] == 'seller')
                                <img class="avatar avatar-lg" src="{{ getValidImage(path: 'storage/app/public/seller/' . $ticket['seller']['image'] ?? '', type: 'backend-profile') }}" alt="">

                                @endif



                                <div class="media-body">
                                    <h6 class="mb-0 {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                                        @if($ticket['Tour'] && $ticket['type'] == 'tour')

                                        {{ $ticket['Tour']['owner_name'] ?? '' }}
                                        @if($ticket['Tour']['owner_name'] != $ticket['Tour']['company_name'])
                                        {{ $ticket['Tour']['company_name'] ?? '' }}
                                        @endif
                                        @elseif($ticket['seller'] && $ticket['type'] == 'seller')
                                        {{ $ticket['seller']['f_name'] ?? '' }}
                                        {{ $ticket['seller']['l_name'] ?? '' }}

                                        @endif
                                        @if($getCountunread > 0)
                                        <a class="__action-btn btn-shadow rounded-full text-success" data-toggle="tooltip" data-title="New Message">(New Message: {{ $getCountunread }})</a>
                                        @endif
                                    </h6>
                                    <div
                                        class="mb-2 fz-12 {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                                        @if($ticket['Tour'] && $ticket['type'] == 'tour')
                                        {{ $ticket['Tour']['email'] ?? '' }}
                                        @elseif($ticket['seller'] && $ticket['type'] == 'seller')
                                        {{ $ticket['seller']['email'] ?? '' }}
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge-soft-{{ (($ticket['status'] == 'close')?'danger':'info')}} fz-12 font-weight-bold px-2 radius-50">{{ translate(str_replace('_', ' ', $ticket['status'])) }}</span>
                                        <h6 class="mb-0">{{ \App\Models\VendorSupportTicket::where('id',($ticket['ticket_id']??''))->first()['message']??"" }}
                                        </h6>
                                    </div>
                                    <div class="text-nowrap mt-2">
                                        @if ($ticket->created_at->diffInDays(Carbon::now()) < 7)
                                            {{ date('D h:i:A', strtotime($ticket->created_at)) }}
                                            @else
                                            {{ date('d M Y h:i:A', strtotime($ticket->created_at)) }}
                                            @endif
                                            </div>
                                    </div>
                                    @else
                                    <h6>{{ translate('customer_not_found') . '!' }}</h6>
                                    @endif
                                </div>
                                <form action="{{ route('admin.vendor-support-ticket.vendor-inbox.status') }}" method="post" id="support-ticket{{ $ticket['id'] }}-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $ticket['id'] }}">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="switcher_input toggle-switch-message"
                                            id="support-ticket{{ $ticket['id'] }}" name="status"
                                            value="{{ $ticket['status'] == 'open' ? 'close' : 'open' }}"
                                            {{ $ticket['status'] == 'open' ? 'checked' : '' }}
                                            data-modal-id="toggle-status-modal"
                                            data-toggle-id="support-ticket{{ $ticket['id'] }}"
                                            data-on-image="support-ticket-on.png"
                                            data-off-image="support-ticket-off.png"
                                            data-on-title="{{ translate('Want_to_Turn_ON_Support_Ticket_Status') . '?' }}"
                                            data-off-title="{{ translate('Want_to_Turn_OFF_Support_Ticket_Status') . '?' }}"
                                            data-on-message="<p>{{ translate('if_enabled_this_support_ticket_will_be_active') }}</p>"
                                            data-off-message="<p>{{ translate('if_disabled_this_support_ticket_will_be_inactive') }}</p>">
                                        <span class="switcher_control"></span>
                                    </label>
                                </form>
                            </div>
                            <div class="card-body align-items-center d-flex flex-wrap flex-md-nowrap justify-content-between gap-4">
                                <div>
                                    {{ $ticket->description }}
                                </div>
                                <div class="text-nowrap">
                                    <a class="btn btn--primary" href="{{ route('admin.vendor-support-ticket.vendor-inbox.singleTicket', $ticket['id']) }}">
                                        <i class="tio-open-in-new"></i> {{ translate('view') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        {{ $getData->links() }}
                    </div>
                </div>
                @if (count($getData) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('image_description') }}">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/support-tickets.js') }}"></script>
    @endpush