@extends('layouts.back-end.app-event')

@section('title', translate('event_details'))
@push('css_or_js')
<style>
    .rainbow {
        background-color: #343A40;
        border-radius: 4px;
        color: #000;
        cursor: pointer;
        padding: 8px 16px;
    }

    .rainbow-1 {
        background-image: linear-gradient(359deg, #90e979d9 13%, #f8f8f8 54%, #ebd859 103%);
        animation: slidebg 5s linear infinite;
    }

    @keyframes slidebg {
        to {
            background-position: 20vw;
        }
    }
</style>
@endpush

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('event_details') }}
        </h2>
    </div>
    <div class="row">
        <div class="card w-100">
            <div class="card-body">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Overview'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link {{ (($name == 'null')?'active':'') }}" id="overview-tab" data-toggle="tab" href="#overview-content">
                            {{ translate('overview') }}
                        </a>
                    </li>
                    @endif
                    @if($view_type == 1)
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Information'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link {{ (($name == 'apevent')?'active':'') }}" id="apevent-tab" data-toggle="tab" href="#apevent-content">
                            {{ translate('Events') }}
                        </a>
                    </li>
                    @endif
                    @endif
                    @if($view_type == 2)
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Information'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="event-info-tab" data-toggle="tab" href="#event-information">
                            {{ translate('All_information') }}
                        </a>
                    </li>
                    @endif
                    @endif
                    @if($view_type == 2)
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Order'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link {{ (($name == 'order')?'active':'') }}" id="order-tab" data-toggle="tab" href="#order-content">
                            {{ translate('order') }}
                        </a>
                    </li>
                    @endif
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Refund'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link {{ (($name == 'refund')?'active':'') }}" id="refund-tab" data-toggle="tab" href="#refund-content">
                            {{ translate('refund') }}
                        </a>
                    </li>
                    @endif 
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'service'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="service-tab" data-toggle="tab" href="#service-content">
                            {{ translate('service') }}
                        </a>
                    </li>
                    @endif
                    @endif
                    @if($view_type == 1)
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Transaction'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#transaction-content">
                            {{ translate('transaction') }}
                        </a>
                    </li>
                    @endif
                    @endif
                    @if($view_type == 2)
                    @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'Review'))
                    <li class="nav-item text-capitalize">
                        <a class="nav-link {{ (($name == 'review')?'active':'') }}" id="review-tab" data-toggle="tab" href="#review-content">
                            {{ translate('review') }}
                        </a>
                    </li>
                    @endif
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade {{ (($name == 'null')?'show active':'') }}" id="overview-content">
                        <div class="row">
                        @include('all-views.event.event-details.overview')
                        </div>
                    </div>
                    @if($view_type == 1)
                    <div class="tab-pane fade {{ (($name == 'apevent')?'show active':'') }}" id="apevent-content">
                        <div class="row">
                            <div class="col-12 px-3 py-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('start_to_end_date') }}" name='start_to_end_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">{{ translate('Submit') }}</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name='name' value="apevent">
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
                                                <input type="hidden" name='name' value="apevent">
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-start">
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
                                                <td><a href="{{route('admin.event-managment.event.event-detail-overview',[$items['event_id']])}}" class='font-weight-bold text-secondary'>{{ ($items['eventid']['unique_id']??"") }}</a></td>
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
                    @endif
                    @if($view_type == 2)
                    <div class="tab-pane fade" id="event-information">
                        <div class="row">
                            @include('all-views.event.event-details.event-information')
                        </div>
                    </div>
                    @endif
                    @if($view_type == 2)
                    <div class="tab-pane fade {{ (($name == 'order')?'show active':'') }}" id="order-content">
                        <div class="row">
                            @include('admin-views.events.event.events_details.order')
                        </div>
                    </div>
                    <div class="tab-pane fade {{ (($name == 'refund')?'show active':'') }}" id="refund-content">
                        <div class="row">
                            @include('admin-views.events.event.events_details.refund')
                        </div>
                    </div>
                    @endif
                    @if($view_type == 2)
                    <div class="tab-pane fade" id="service-content">
                        <div class="row">
                            @include('admin-views.events.event.events_details.service')
                        </div>
                    </div>
                    @endif

                    <div class="tab-pane fade" id="transaction-content">
                        <div class="row">

                            @if($view_type == 1)
                            @include('admin-views.events.event.events_details.eventapprotransaction')
                            @endif
                        </div>
                    </div>
                    @if($view_type == 2)
                    <div class="tab-pane fade {{ (($name == 'review')?'show active':'') }}" id="review-content">
                        <div class="row">
                            @include('admin-views.events.event.events_details.review')
                        </div>
                    </div>
                    @endif
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

<!--  -->

<!-- Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Package Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Dynamic content will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    let getYesWord = $('#message-yes-word').data('text');
    let getCancelWord = $('#message-cancel-word').data('text');
    $('.reject-artist_data').on('click', function() {
        let astrologerId = $(this).attr("data-id");
        Swal.fire({
            title: 'Are You Sure To Reject Event',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: getYesWord,
            cancelButtonText: getCancelWord,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#' + astrologerId).submit();
            }
        });
    });

    $(document).ready(function() {
        var totalOrderAmount = 0;
        var totalFinalAmount = 0;
        var totalcommission = 0;
        var totalgovttax = 0;

        // Iterate over each row in the tbody
        $('#datatable_transaction tbody tr').each(function() {
            // Get the order amount and final amount from each row
            var orderAmount = parseFloat($(this).find('.order_amount').text()) || 0;
            var finalAmount = parseFloat($(this).find('.final_amount').text()) || 0;
            var commission = parseFloat($(this).find('.order_commission').text()) || 0;
            var govttax = parseFloat($(this).find('.order_govt_tax').text()) || 0;

            // Add the amounts to the total
            totalOrderAmount += orderAmount;
            totalFinalAmount += finalAmount;
            totalcommission += commission;
            totalgovttax += govttax;
        });

        // Display the totals in the footer
        $('#totalOrderAmount').text(totalOrderAmount.toFixed(2));
        $('#totalFinalAmount').text(totalFinalAmount.toFixed(2));
        $('#totalOrdercommission').text(totalcommission.toFixed(2));
        $('#totalOrdergovttax').text(totalgovttax.toFixed(2));
    });

    function orderitemviews(order_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('event-vendor.event-order.event-order-view') }}",
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

    function getAllPackage(that) {
        var htmlContent = $(that).data('html');
        $('#modalBodyContent').html(htmlContent);
        $('#dataModal').modal('show');
    }

    function showDetailsButton(that) {
        var point = $(that).data('point');
        var availableSoldRows = $(`.available-sold-row${point}`);

        if (availableSoldRows.hasClass('d-none')) {
            availableSoldRows.removeClass('d-none').addClass('visible');
        } else {
            availableSoldRows.removeClass('visible').addClass('d-none');
        }
    }
</script>
@endpush