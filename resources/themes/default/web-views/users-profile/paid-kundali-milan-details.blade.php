@extends('layouts.front-end.app')

@section('title', translate('kundali'))

@section('content')

<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
    <div class="row">
        @include('web-views.partials._profile-aside')

        <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
            <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                <div class="card-body">
                    <div class="align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <h5 class="font-bold mb-0 fs-16">{{ translate('paid_Kundali') }} {{ (($kundalis['birthJournal']['name'] == 'kundali_milan')?"Milan":"") }}</h5><br>
                        <h4 class="font-bold mb-0 fs-14">{{ translate('Order') }} : #{{ $kundalis['order_id']}}
                            <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ (($kundalis['milan_verify'] == 1)?'success':'primary')}} fs-12 font-semibold text-capitalize">{{ (($kundalis['milan_verify'] == 1)?'success':"Pending")}} </span>
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn--primary px-4 float-end" target="_blank" href="{{ route('kundali-generate-invoice', $kundalis['id']) }}">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}" alt="" class="mr-1">
                                    {{ translate('print_Invoice') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link __inline-27 active" href="#all_order" data-toggle="tab" role="tab">
                                        {{translate('order_summary')}}
                                    </a>
                                </li>
                                @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#track_order" data-toggle="tab" role="tab">
                                        {{translate('track_order')}}
                                    </a>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content px-lg-3">
                                <div class="tab-pane fade show active text-justify" id="all_order" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-1">
                                                <div class="card body">
                                                    <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                                        <table class="table table-borderless mb-0">
                                                            <thead>
                                                                <tr class="order_table_tr">
                                                                    <td class="order_table_td">
                                                                        <div class="">
                                                                            <div class="_1 py-2 d-flex justify-content-between align-items-center">
                                                                                <h6 class="fs-13 font-bold text-capitalize">{{translate('payment_info')}}</h6>
                                                                            </div>
                                                                            <div class="fs-12">
                                                                                <span class="text-muted text-capitalize">{{translate('payment_status')}}</span>:
                                                                                @if ($kundalis['payment_status'] == 1)
                                                                                <span class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                                                @else
                                                                                <span class="text-success text-capitalize">{{ translate('unpaid') }}</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="mt-2 fs-12">
                                                                                <span class="text-muted text-capitalize">{{translate('payment_method')}}</span> :<span class="text-primary text-capitalize">
                                                                                    @if($kundalis['transaction_id'] == 'wallet')
                                                                                    {{ translate('Wallet') }}
                                                                                    @else
                                                                                    {{ translate('online') }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                            <div class="mt-2 fs-12">
                                                                                <span>Type : {{ str_replace('_',' ',$kundalis['birthJournal']['name'])}}</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="order_table_td">
                                                                        <div class="">
                                                                            <div class="py-2">
                                                                                <h6 class="fs-13 font-bold text-capitalize">
                                                                                    {{translate('User_info')}}:
                                                                                </h6>
                                                                            </div>
                                                                            <div class="">
                                                                                <span class="text-capitalize fs-12">
                                                                                    <span class="text-capitalize">
                                                                                        <span
                                                                                            class="min-w-60px">{{translate('name')}}</span> : &nbsp;{{($kundalis['userData']['name']??"")}}
                                                                                    </span>
                                                                                    <br>
                                                                                    <span class="text-capitalize">
                                                                                        <span
                                                                                            class="min-w-60px">{{translate('phone')}}</span> : &nbsp;{{ ($kundalis['userData']['phone']??"")}},
                                                                                    </span>
                                                                                    <br>
                                                                                    <span class="text-capitalize">
                                                                                        <span
                                                                                            class="min-w-60px">{{translate('email')}}</span> : &nbsp;{{($kundalis['userData']['email']??"")}},
                                                                                    </span>

                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card-2 border-sm">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h4 class="text-center font-weight-bolder">
                                                                @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                                {{ translate('Info_For_kundali_Milan') }}
                                                                @else
                                                                {{ translate('Info_For_kundali') }}
                                                                @endif
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-{{(($kundalis['birthJournal']['name'] == 'kundali_milan')?'6':'12')}}">
                                                            @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-center"><b>{{ (($kundalis['gender'] == 'male')? translate('Male') : translate('Female') ) }}</b>
                                                                    <hr>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="row mb-2">
                                                                <div class="col-6 ">{{ translate('Name') }}</div>
                                                                <div class="col-6">{{ ($kundalis['name']??"") }}</div>
                                                            </div>

                                                            <!-- {{-- <div class="row mb-2">
                                                            <div class="col-6 ">
                                                                {{ translate('Email') }}
                                                            </div>
                                                            <div class="col-6">
                                                                {{ ($kundalis['email']??"") }}
                                                            </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Phone_Number') }}</div>
                                                                <div class="col-6">{{ ($kundalis['phone_no']??"") }}</div>
                                                            </div> --}} -->

                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('date_of_birth') }}</div>
                                                                <div class="col-6">{{ ($kundalis['bod']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('time') }}</div>
                                                                <div class="col-6">{{ ($kundalis['time']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Country') }}</div>
                                                                <div class="col-6">{{ ($kundalis['country']['name']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('place') }}</div>
                                                                <div class="col-6">{{ ($kundalis['state']??"") }}</div>
                                                            </div>
                                                        </div>
                                                        @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                        <div class="col-md-6">
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-center"><b>{{ (($kundalis['female_gender'] == 'male')? translate('Male') : translate('Female') ) }}</b>
                                                                    <hr>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6 ">{{ translate('Name') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_name']??"") }}</div>
                                                            </div>
                                                            <!-- {{--<div class="row mb-2">
                                                            <div class="col-6 ">{{ translate('Email') }} </div>
                                                            <div class="col-6">{{ ($kundalis['female_email']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Phone_Number') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_phone_no']??"") }}</div>
                                                            </div>--}} -->
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('date_of_birth') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_dob']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('time') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_time']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Country') }}</div>
                                                                <div class="col-6">{{ ($kundalis['country_female']['name']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('place') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_place']??"") }}</div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row d-flex justify-content-end mt-2">
                                                <div class="col-md-8 col-lg-5">
                                                    <div class="bg-white border-sm rounded">
                                                        <div class="card-body ">
                                                            <table class="calculation-table table table-borderless mb-0">
                                                                <tbody class="totals">
                                                                    <tr>
                                                                        <td>
                                                                            <div class="text-start">
                                                                                <span class="font-semibold">{{translate('item')}}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="text-end">
                                                                                <span class="font-semibold">{{translate('Price')}}</span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="border-top">
                                                                        <td>
                                                                            <div class="text-start">
                                                                                <span class="product-qty">{{translate('subtotal')}}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>

                                                                            <div class="text-end">
                                                                                <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($kundalis['amount'])), currencyCode: getCurrencyCode()) }}</span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="border-top">
                                                                        <td>
                                                                            <div class="text-start">
                                                                                <span class="font-weight-bold">
                                                                                    <strong>{{translate('total_Price')}}</strong>
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="text-end">
                                                                                <span class="font-weight-bold amount">
                                                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kundalis['amount']), currencyCode: getCurrencyCode()) }}
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
                                    </div>
                                </div>
                                <div class="tab-pane fade text-justify" id="track_order" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <ul class="nav nav-tabs media-tabs nav-justified order-track-info">
                                                        <li class="nav-item">
                                                            <div class="nav-link active-status">
                                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                                    <div class="media-tab-media mx-sm-auto mb-3">
                                                                        <img src="{{asset('/public/assets/front-end/img/track-order/order-placed.png')}}" alt="">
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <div class="text-sm-center">
                                                                            <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">Order placed</h6>
                                                                        </div>
                                                                        <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                                            <img src="{{asset('/public/assets/front-end/img/track-order/clock.png')}}" width="14" alt="">
                                                                            <span class="text-muted fs-12">{{date('h:i A, d M Y', strtotime($kundalis['created_at']))}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </li>
                                                        <li class="nav-item">
                                                            <div class="nav-link {{$kundalis['milan_verify']==1?'active-status':''}}">
                                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                                        <img src="{{asset('public/assets/front-end/img/maleFemale.png')}}" alt="">
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <div class="text-sm-center">
                                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Completed</h6>
                                                                        </div>
                                                                        @if ($kundalis['milan_verify']==1)
                                                                        <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                                            <img src="{{asset('/public/assets/front-end/img/track-order/clock.png')}}" width="14" alt="">
                                                                            <span class="text-muted fs-12">{{date('h:i A, d M Y', strtotime($kundalis['updated_at']))}}</span>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>
            </div>
            <div class="bg-white d-lg-none web-direction">
                <div class="card-body d-flex flex-column gap-3 customer-profile-orders py-0">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <div class="align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                            <h5 class="font-bold mb-0 fs-16">{{ translate('paid_Kundali') }} {{ (($kundalis['birthJournal']['name'] == 'kundali_milan')?"Milan":"") }}</h5><br>
                            <h4 class="font-bold mb-0 fs-14">{{ translate('Order') }} : #{{ $kundalis['order_id']}}
                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ (($kundalis['milan_verify'] == 1)?'success':'primary')}} fs-12 font-semibold text-capitalize">{{ (($kundalis['milan_verify'] == 1)?'success':"Pending")}} </span>
                            </h4>
                        </div>
                        <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 15 15" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                    <div class="d-flex border-lighter rounded p-2 justify-content-between gap-1">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link __inline-27 active" href="#all_order" onclick="$('.track_order-tabs').removeClass('show active');$('.all_order-tabs').addClass('show active');" data-toggle="tab" role="tab">
                                            {{translate('order_summary')}}
                                        </a>
                                    </li>
                                    @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                    <li class="nav-item">
                                        <a class="nav-link __inline-27" href="#track_order" onclick="$('.track_order-tabs').addClass('show active');$('.all_order-tabs').removeClass('show active');" data-toggle="tab" role="tab">
                                            {{translate('track_order')}}
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                                <div class="tab-content px-lg-3">
                                    <div class="tab-pane fade show active text-justify all_order-tabs" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-1">
                                                    <div class="card body">
                                                        <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                                            <div>
                                                                <a class="btn btn--primary px-2 mt-2 mr-2  float-end" target="_blank" href="{{ route('kundali-generate-invoice', $kundalis['id']) }}">
                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}" alt="" class="mr-1">                                                                   
                                                                </a>
                                                            </div>
                                                            <table class="table table-borderless mb-0">
                                                                <thead>
                                                                    <tr class="order_table_tr">
                                                                        <td class="order_table_td">
                                                                            <div class="">
                                                                                <div class="_1 py-2 d-flex justify-content-between align-items-center">
                                                                                    <h6 class="fs-13 font-bold text-capitalize">{{translate('payment_info')}}</h6>
                                                                                </div>
                                                                                <div class="fs-12">
                                                                                    <span class="text-muted text-capitalize">{{translate('payment_status')}}</span>:
                                                                                    @if ($kundalis['payment_status'] == 1)
                                                                                    <span class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                                                    @else
                                                                                    <span class="text-success text-capitalize">{{ translate('unpaid') }}</span>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="mt-2 fs-12">
                                                                                    <span class="text-muted text-capitalize">{{translate('payment_method')}}</span> :<span class="text-primary text-capitalize">
                                                                                        @if($kundalis['transaction_id'] == 'wallet')
                                                                                        {{ translate('Wallet') }}
                                                                                        @else
                                                                                        {{ translate('online') }}
                                                                                        @endif
                                                                                    </span>
                                                                                </div>
                                                                                <div class="mt-2 fs-12">
                                                                                    <span>Type : {{ str_replace('_',' ',$kundalis['birthJournal']['name'])}}</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="order_table_td">
                                                                            <div class="">
                                                                                <div class="py-2">
                                                                                    <h6 class="fs-13 font-bold text-capitalize">
                                                                                        {{translate('User_info')}}:
                                                                                    </h6>
                                                                                </div>
                                                                                <div class="">
                                                                                    <span class="text-capitalize fs-12">
                                                                                        <span class="text-capitalize">
                                                                                            <span
                                                                                                class="min-w-60px">{{translate('name')}}</span> : &nbsp;{{($kundalis['userData']['name']??"")}}
                                                                                        </span>
                                                                                        <br>
                                                                                        <span class="text-capitalize">
                                                                                            <span
                                                                                                class="min-w-60px">{{translate('phone')}}</span> : &nbsp;{{ ($kundalis['userData']['phone']??"")}},
                                                                                        </span>
                                                                                        <br>
                                                                                        <span class="">
                                                                                            <span class="min-w-60px">{{translate('Email')}}</span> : <span style="text-transform: lowercase;">&nbsp;{{($kundalis['userData']['email']??"")}}</span>,
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="card-2 border-sm">
                                                    <div class="card-body" style="font-size: 13px;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5 class="text-center font-weight-bolder">
                                                                    @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                                    {{ translate('Info_For_kundali_Milan') }}
                                                                    @else
                                                                    {{ translate('Info_For_kundali') }}
                                                                    @endif
                                                                </h5>
                                                            </div>
                                                            <div class="col-md-{{(($kundalis['birthJournal']['name'] == 'kundali_milan')?'6':'12')}}">
                                                                @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                                <div class="row mb-2">
                                                                    <div class="col-12 text-center"><b>{{ (($kundalis['gender'] == 'male')? translate('Male') : translate('Female') ) }}</b>
                                                                        <hr>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <div class="row mb-2">
                                                                    <div class="col-6 ">{{ translate('Name') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['name']??"") }}</div>
                                                                </div>

                                                                <!-- {{-- <div class="row mb-2">
                                                            <div class="col-6 ">
                                                                {{ translate('Email') }}
                                                            </div>
                                                            <div class="col-6">
                                                                {{ ($kundalis['email']??"") }}
                                                            </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Phone_Number') }}</div>
                                                                <div class="col-6">{{ ($kundalis['phone_no']??"") }}</div>
                                                            </div> --}} -->

                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('date_of_birth') }}</div>
                                                                    <div class="col-6">{{ date("d M,Y",strtotime($kundalis['bod']??"")) }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('time') }}</div>
                                                                    <div class="col-6">{{ date("h:i A",strtotime($kundalis['time']??"")) }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('Country') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['country']['name']??"") }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('place') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['state']??"") }}</div>
                                                                </div>
                                                            </div>
                                                            @if($kundalis['birthJournal']['name'] == 'kundali_milan')
                                                            <div class="col-md-6">
                                                                <div class="row mb-2">
                                                                    <div class="col-12 text-center"><b>{{ (($kundalis['female_gender'] == 'male')? translate('Male') : translate('Female') ) }}</b>
                                                                        <hr>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6 ">{{ translate('Name') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['female_name']??"") }}</div>
                                                                </div>
                                                                <!-- {{--<div class="row mb-2">
                                                            <div class="col-6 ">{{ translate('Email') }} </div>
                                                            <div class="col-6">{{ ($kundalis['female_email']??"") }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-6">{{ translate('Phone_Number') }}</div>
                                                                <div class="col-6">{{ ($kundalis['female_phone_no']??"") }}</div>
                                                            </div>--}} -->
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('date_of_birth') }}</div>
                                                                    <div class="col-6">{{ date("d M,Y",strtotime($kundalis['female_dob']??"")) }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('time') }}</div>
                                                                    <div class="col-6">{{ date("h:i A",strtotime($kundalis['female_time']??"")) }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('Country') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['country_female']['name']??"") }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-6">{{ translate('place') }}</div>
                                                                    <div class="col-6">{{ ($kundalis['female_place']??"") }}</div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row d-flex justify-content-end mt-2">
                                                    <div class="col-md-8 col-lg-5">
                                                        <div class="bg-white border-sm rounded">
                                                            <div class="card-body ">
                                                                <table class="calculation-table table table-borderless mb-0">
                                                                    <tbody class="totals">
                                                                        <tr>
                                                                            <td>
                                                                                <div class="text-start">
                                                                                    <span class="font-semibold">{{translate('item')}}</span>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="text-end">
                                                                                    <span class="font-semibold">{{translate('Price')}}</span>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="border-top">
                                                                            <td>
                                                                                <div class="text-start">
                                                                                    <span class="product-qty">{{translate('subtotal')}}</span>
                                                                                </div>
                                                                            </td>
                                                                            <td>

                                                                                <div class="text-end">
                                                                                    <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($kundalis['amount'])), currencyCode: getCurrencyCode()) }}</span>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="border-top">
                                                                            <td>
                                                                                <div class="text-start">
                                                                                    <span class="font-weight-bold">
                                                                                        <strong>{{translate('total_Price')}}</strong>
                                                                                    </span>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="text-end">
                                                                                    <span class="font-weight-bold amount">
                                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kundalis['amount']), currencyCode: getCurrencyCode()) }}
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
                                        </div>
                                    </div>
                                    <div class="tab-pane fade text-justify track_order-tabs" role="tabpanel">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <ul class="nav nav-tabs media-tabs nav-justified order-track-info">
                                                            <li class="nav-item">
                                                                <div class="nav-link active-status">
                                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                                        <div class="media-tab-media mx-sm-auto mb-3">
                                                                            <img src="{{asset('/public/assets/front-end/img/track-order/order-placed.png')}}" alt="">
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <div class="text-sm-center">
                                                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">Order placed</h6>
                                                                            </div>
                                                                            <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                                                <img src="{{asset('/public/assets/front-end/img/track-order/clock.png')}}" width="14" alt="">
                                                                                <span class="text-muted fs-12">{{date('h:i A, d M Y', strtotime($kundalis['created_at']))}}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </li>
                                                            <li class="nav-item">
                                                                <div class="nav-link {{$kundalis['milan_verify']==1?'active-status':''}}">
                                                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                                        <div class="media-tab-media mb-3 mx-sm-auto">
                                                                            <img src="{{asset('public/assets/front-end/img/maleFemale.png')}}" alt="">
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <div class="text-sm-center">
                                                                                <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Completed</h6>
                                                                            </div>
                                                                            @if ($kundalis['milan_verify']==1)
                                                                            <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                                                <img src="{{asset('/public/assets/front-end/img/track-order/clock.png')}}" width="14" alt="">
                                                                                <span class="text-muted fs-12">{{date('h:i A, d M Y', strtotime($kundalis['updated_at']))}}</span>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </section>
    </div>

</div>

@endsection