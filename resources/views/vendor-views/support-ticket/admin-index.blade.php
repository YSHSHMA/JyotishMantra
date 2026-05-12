@php use Carbon\Carbon; @endphp
@extends('layouts.back-end.app-seller')

@section('title', translate('support_Ticket'))

@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-8">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/support_ticket.png') }}"
                    alt="">
                {{ translate('support_ticket') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $message_list->total() }}</span>
            </h2>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('All_query')}}</h6>
                </div>
                <span class="order-stats__title"> {{ \App\Models\VendorSupportTicketConv::where('created_by','admin')->where('type','seller')->where('vendor_id',auth('seller')->id())->whereNotNull('ticket_id')->count()}} </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('Open')}}</h6>
                </div>
                <span class="order-stats__title">{{ \App\Models\VendorSupportTicketConv::where('created_by','admin')->where('type','seller')->where('vendor_id',auth('seller')->id())->where('status','open')->count()}}</span>
                </span>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
            <a class="order-stats order-stats_confirmed">
                <div class="order-stats__content">
                    <i class="tio-all_done">all_done</i>
                    <h6 class="order-stats__subtitle">{{ translate('Close')}}</h6>
                </div>
                <span class="order-stats__title">{{ \App\Models\VendorSupportTicketConv::where('created_by','admin')->where('type','seller')->where('vendor_id',auth('seller')->id())->where('status','close')->count()}} </span>
                </span>
            </a>
        </div>
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
                        <div class="row">
                            
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-3 justify-content-end">
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
                @foreach ($message_list->reverse() as $key => $ticket)
                <div class="border-bottom mb-3 pb-3">
                    <div class="card">
                        <div
                            class="card-body align-items-center d-flex flex-wrap justify-content-between gap-3 border-bottom">
                            <div class="media gap-3">
                                <?php
                                $getCountunread = \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $ticket['id'])->where('read_user_status', 0)->count();

                                ?>
                                @if($ticket['seller'] && $ticket['type'] == 'seller')
                                <img class="avatar avatar-lg" src="{{ getValidImage(path: 'storage/app/public/seller/' . $ticket['seller']['image'] ?? '', type: 'backend-profile') }}" alt="">
                                <div class="media-body">
                                    <h6
                                        class="mb-0 {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                                        {{ $ticket['seller']['f_name'] ?? '' }}
                                        {{ $ticket['seller']['l_name'] ?? '' }}

                                        @if($getCountunread > 0)
                                        <a class="__action-btn btn-shadow rounded-full text-success" data-toggle="tooltip" data-title="New Message">(New Message: {{ $getCountunread }})</a>
                                        @endif
                                    </h6>
                                    <div
                                        class="mb-2 fz-12 {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                                        {{ $ticket['seller']['email'] ?? '' }}
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        {{-- <span class="badge-soft-danger fz-12 font-weight-bold px-2 radius-50">{{ translate(str_replace('_', ' ', $ticket->priority)) }}</span> --}}
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
                                @if($ticket['status'] == 'open')
                                <form action="{{ route('vendor.support-ticket.vendor.status') }}" method="post" id="support-ticket{{ $ticket['id'] }}-form">
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
                                @endif
                            </div>
                            <div
                                class="card-body align-items-center d-flex flex-wrap flex-md-nowrap justify-content-between gap-4">
                                <div>
                                    {{ $ticket->description }}
                                </div>
                                <div class="text-nowrap">
                                    <a class="btn btn--primary"
                                        href="{{ route('vendor.support-ticket.admin.singleTicket', $ticket['id']) }}">
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
                        {{ $message_list->links() }}
                    </div>
                </div>
                @if (count($message_list) == 0)
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