@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Event_order_details'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Event_order_details') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card mt-20">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-20">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 text-start">Event Name </div>
                                        <div class="col-6 text-end p-0">{{ $getData['eventid']['event_name']??""}} </div>
                                        <div class="col-12">&nbsp; </div>
                                        <div class="col-6 text-start">Order Id </div>
                                        <div class="col-6 text-end p-0">#{{ $getData['order_no']??""}} </div>
                                        <div class="col-12">&nbsp; </div>
                                        <div class="col-6 text-start">Event Category </div>
                                        <div class="col-6 text-end p-0">{{ ($getData['eventid']['categorys']['category_name']??'') }} </div>
                                        <div class="col-12">&nbsp; </div>
                                        <div class="col-6 text-start">Event Venue </div>
                                        <?php
                                        $venue_name = '';
                                        $create_date = '';
                                        if (!empty($getData['eventid']['all_venue_data']) && json_decode($getData['eventid']['all_venue_data'], true)) {
                                            $venue_name = array_filter(json_decode($getData['eventid']['all_venue_data'], true), function ($event) use ($getData) {
                                                return ($event['id'] ?? "") == $getData['venue_id'];
                                            });
                                            $venue_name = reset($venue_name);
                                        } ?>
                                        <div class="col-6 text-end p-0"><span>{{ ($venue_name['en_event_venue']??"") }}</span> </div>
                                        <div class="col-12">&nbsp; </div>
                                        <div class="col-6 text-start">Event Date </div>
                                        <div class="col-6 text-end p-0">{{ date('d M,Y',strtotime($venue_name['date']??"")) }} {{ ($venue_name['start_time']??"") }} </div>
                                        <div class="col-12">&nbsp; </div>
                                        <div class="col-6 text-start">Event Org </div>
                                        <div class="col-6 text-end p-0 font-weight-bolder">{{ ($getData['eventid']['organizer_by']??'') }} </div>
                                        <div class="col-12">&nbsp; </div>                                        

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <a class="btn btn--primary px-4" target="_blank"
                                                href="{{ route('admin.event-managment.event-booking.booking-invoice', $getData['id']) }}">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}" alt="" class="mr-1">
                                                {{ translate('print_Invoice') }}
                                            </a>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Status : <span class="badge badge-{{ (($getData['status'] == 1)?'success':'danger')}} font-weight-bold radius-50 align-items-center py-1 px-2">{{ (($getData['status'] == 1)?"Success":'Pending')}}</span></span>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Payment Method : <span class="font-weight-bold">{{ (($getData['transaction_id'] == 'wallet')?"wallet":'Online')}}</span></span>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Payment status : <span class="font-weight-bold text-{{ (($getData['transaction_status'] == 1)?'success':'danger')}}">{{ (($getData['transaction_status'] == 1)?"Paid":'Unpaid')}}</span></span>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <span>Booking Date : <span>{{ date('d M,Y h:i A',strtotime($getData['created_at'])) }}</span></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                    <div class="row mt-4">
                        <div class="table-responsive datatable-custom">
                            <table class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>Package Name</th>
                                        <th>No. Of Ticket</th>
                                        <th>Amount</th>
                                        <th>Final Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($getData['orderitem'])
                                    @foreach($getData['orderitem'] as $pval)
                                    <tr>
                                        <td>
                                            <div class="media align-items-center gap-10">
                                                <div>
                                                    <h6 class="title-color">
                                                        {{ ucwords(str_replace("_"," ",($pval['category']['package_name']??""))) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $pval['no_of_seats']??'' }}
                                        </td>
                                        <td>
                                            <?php $totalAmounts = (($getData['coupon_amount']??0) + ($pval['amount']??0)); ?>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (($totalAmounts ?? 0) / ($pval['no_of_seats'] ?? 0))), currencyCode: getCurrencyCode()) }}
                                        </td>
                                        <td>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalAmounts ?? 0), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body ">
                            <table class="calculation-table table table-borderless mb-0">
                                <tbody class="totals">
                                    <tr class="border-top">
                                        <td>
                                            <div class="text-start">
                                                <span class="product-qty">{{ translate('coupon_amount') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-end">
                                                <span
                                                    class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['coupon_amount']), currencyCode: getCurrencyCode()) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td>
                                            <div class="text-start">
                                                <span class="font-weight-bold">
                                                    <strong>{{ translate('total_Price') }}</strong>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-end">
                                                <span class="font-weight-bold amount">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['amount']), currencyCode: getCurrencyCode()) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h4 class="d-flex gap-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                        alt="">
                                    {{ translate('customer_information') }}
                                </h4>
                            </div>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70"
                                        src="{{ getValidImage(path: 'storage/app/public/profile/' . $getData['userData']['image'], type: 'backend-basic') }}"
                                        alt="{{ translate('Image') }}">
                                </div>
                                <div class="media-body d-flex flex-column align-self-center gap-1">
                                    <span class="title-color">
                                        <strong>{{ $getData['userData']['name'] }}</strong>
                                    </span>
                                    <span
                                        class="title-color break-all"><strong>{{ $getData['userData']['phone'] }}</strong></span>
                                    @if (str_contains($getData['userData']['email'], '.com'))
                                    <span class="title-color break-all"><strong>{{ $getData['userData']['email'] }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection