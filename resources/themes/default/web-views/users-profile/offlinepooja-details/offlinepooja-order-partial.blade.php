@php
    $payment_gateways_list = App\Utils\payment_gateways();
@endphp

{{-- schedule modall --}}
@if ($order->is_edited==1)
    
<div class="modal fade" id="schedule-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('schedule_Pooja') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @foreach ($payment_gateways_list as $payment_gateway)
                <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                    action="{{ route('customer.offlinepooja-schedule-payment-request') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label
                                        class="form-label font-semibold">{{ translate('new_booking_date') }}(Compulsory)</label>
                                    <input class="form-control text-align-direction" type="date" name="booking_date" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            @if ($order->pooja_venue_type == 'address')
                            <div class="col-12">
                                <div class="form-group">
                                    <label
                                        class="form-label font-semibold">{{ translate('venue_address ') }}(Compulsory)</label>
                                    <input class="form-control text-align-direction" type="text"
                                        id="google-search" value="{{ $order->venue_address }}" disabled>
                                    {{-- <input class="form-control" type="hidden" name="state" id="state"
                                        placeholder="state" value="{{ $order->state }}">
                                    <input class="form-control" type="hidden" name="city" id="city"
                                        placeholder="city" value="{{ $order->city }}">
                                    <input class="form-control" type="hidden" name="pincode" id="pincode"
                                        placeholder="pincode" value="{{ $order->pincode }}">
                                    <input class="form-control" type="hidden" name="latitude" id="latitude"
                                        placeholder="latitude" value="{{ $order->latitude }}">
                                    <input class="form-control" type="hidden" name="longitude" id="longitude"
                                        placeholder="longitude" value="{{ $order->longitude }}"> --}}
                                </div>
                            </div>
                            @if (!empty($order->landmark))
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label
                                        class="form-label font-semibold">{{ translate('landmark ') }}(Compulsory)</label>
                                    <input class="form-control text-align-direction" type="text" value="{{ $order->landmark }}" disabled>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label
                                        class="form-label font-semibold">{{ translate('temple ') }}</label>
                                    <input class="form-control text-align-direction" type="text" value="{{ $order->temple->name??'temple not found' }}" disabled>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row mt-3">
                            <div class="offset-md-7 col-md-5">
                                <h5>{{ translate('payment_information') }}</h5>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p>{{ translate('available_wallet_balance') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="schedule-available-wallet-balance"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('schedule_pooja_charge') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="schedule-pooja-charge"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('wallet_deduction') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="schedule-wallet-deduction"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('pay_online') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="schedule-pay-online"></span></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="Details">
                            <input type="hidden" name="user_id"
                                value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                            <input type="hidden" name="customer_id"
                                value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                            <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                            <input type="hidden" name="payment_platform" value="web">
                            @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                <input type="hidden" name="callback"
                                    value="{{ $payment_gateway->live_values['callback_url'] }}">
                            @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                <input type="hidden" name="callback"
                                    value="{{ $payment_gateway->test_values['callback_url'] }}">
                            @else
                                <input type="hidden" name="callback" value="">
                            @endif
                            <input type="hidden" name="external_redirect_link"
                                value="{{ url('/') . '/offlinepooja-schedule-web-payment' }}">
                            <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment"
                                    class="form-check-input custom-radio" value="{{ $payment_gateway->key_name }}"
                                    hidden>
                                <img width="30"
                                    src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                    alt="" hidden>
                                <span class="text-capitalize form-check-label" hidden>
                                    @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                        {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                    @else
                                        {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                    @endif
                                </span>
                            </label>

                            <input type="hidden" name="wallet_deduction" id="schedule-wallet-deduction-input">
                            <input type="hidden" name="payment_amount" id="schedule-pay-online-input">
                            <input type="hidden" name="order_id" id="" value="{{ $order->order_id }}">
                        </div>
                        <div class="">
                            <button type="submit"
                                class="btn btn-primary btn-block">{{ translate('reschedule') }}</button>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- remaining pay modal --}}
<div class="modal fade" id="remaining-pay-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('pay_Remaining_Amount') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @foreach ($payment_gateways_list as $payment_gateway)
                <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                    action="{{ route('customer.offlinepooja-remaining-payment-request') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mt-3">
                            <div class="col-md-7 text-center align-content-center">
                                <img src="{{ asset('storage/app/public/offlinepooja/thumbnail/' . $order['offlinePooja']['thumbnail']) }}"
                                    alt="" width="150">
                                <h6 class="mt-2">{{ $order['offlinePooja']['name'] }}</h6>
                            </div>
                            <div class="col-md-5">
                                <h6>{{ translate('payment_information') }}</h6>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p>{{ translate('available_wallet_balance') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="remaining-available-wallet-balance"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('remaining_amount') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span>{{ $order->remain_amount }}</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('wallet_deduction') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="remaining-wallet-balance-deduction"></span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{{ translate('pay_online') }}:</p>
                                            </td>
                                            <td>
                                                <p>₹<span id="remaining-pay-online"></span></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="Details">
                                <input type="hidden" name="user_id"
                                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                <input type="hidden" name="customer_id"
                                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                <input type="hidden" name="payment_method"
                                    value="{{ $payment_gateway->key_name }}">
                                <input type="hidden" name="payment_platform" value="web">
                                @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                    <input type="hidden" name="callback"
                                        value="{{ $payment_gateway->live_values['callback_url'] }}">
                                @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                    <input type="hidden" name="callback"
                                        value="{{ $payment_gateway->test_values['callback_url'] }}">
                                @else
                                    <input type="hidden" name="callback" value="">
                                @endif
                                <input type="hidden" name="external_redirect_link"
                                    value="{{ url('/') . '/offlinepooja-remaining-web-payment' }}">
                                <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                    <input type="radio" id="{{ $payment_gateway->key_name }}"
                                        name="online_payment" class="form-check-input custom-radio"
                                        value="{{ $payment_gateway->key_name }}" hidden>
                                    <img width="30"
                                        src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                        alt="" hidden>
                                    <span class="text-capitalize form-check-label" hidden>
                                        @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                            {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                        @else
                                            {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                        @endif
                                    </span>
                                </label>

                                {{-- <input type="hidden" name="payment_amount" id="remaining"
                                    value="{{ $order->remain_amount }}">
                                <input type="hidden" name="transection_amount" id=""
                                    value="{{ $order->remain_amount + $order->transection_amount }}">
                                <input type="hidden" name="pay_amount" id=""
                                    value="{{ $order->remain_amount + $order->pay_amount }}">
                                <input type="hidden" name="order_id" id=""
                                    value="{{ $order->order_id }}"> --}}

                                <input type="hidden" name="wallet_deduction" id="remaining-wallet-deduction-input">
                                <input type="hidden" name="payment_amount" id="remaining-pay-online-input">
                                <input type="hidden" name="order_id" id=""
                                    value="{{ $order->order_id }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="">
                            <button type="submit" class="btn btn-primary btn-block">{{ translate('pay') }}</button>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</div>

{{-- cancel pooja modal --}}
<div class="modal fade" id="cancel-pooja-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('cancel_Pooja_Order') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('offlinepooja-cancle-order-submit') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mt-3">
                        <div class="col-md-7 text-center align-content-center">
                            <img src="{{ asset('storage/app/public/offlinepooja/thumbnail/' . $order['offlinePooja']['thumbnail']) }}"
                                alt="" width="150">
                            <h6 class="mt-2">{{ $order['offlinePooja']['name'] }}</h6>
                        </div>
                        <div class="col-md-5">
                            <h6>{{ translate('payment_information') }}</h6>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <p>{{ translate('pooja_price') }}:</p>
                                        </td>
                                        <td>
                                            <p>₹<span>{{ $order->package_main_price }}</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>{{ translate('you_paid') }}:</p>
                                        </td>
                                        <td>
                                            <p>₹<span>{{ $order->pay_amount }}</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>{{ translate('refund_amount') }}:</p>
                                        </td>
                                        <td>
                                            <p>₹<span id="refund-amount"></span></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="refund_amount" id="refund-amount-input">
                        <input type="hidden" name="order_id" id="" value="{{ $order->order_id }}">
                    </div>
                    <div class="row">
                        <label for="">Cancle Reason</label>
                        <textarea name="order_canceled_reason" class="form-control" rows="3" placeholder="Enter your reason" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="">
                        <button type="submit" class="btn btn-primary btn-block">{{ translate('submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- main page --}}
<div class="d-flex align-items-start justify-content-between gap-2">
    <div class="w-100">
        <div class="row">
            <div id="schedule-modal-trigger" data-base-url="{{ url('/') }}"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12 d-flex align-items-center gap-2 text-capitalize">
                        <h4 class="text-capitalize mb-0 mobile-fs-14 fs-18 font-bold">{{ translate('order') }}
                            #{{ $order->order_id }} </h4>
                        <span
                            class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $order->status == 0 ? 'primary' : ($order->status == 1 ? 'success' : ($order->status == 2 ? 'danger' : ($order->status == 3 ? 'primary' : 'primary'))) }} fs-12 font-semibold text-capitalize ">{{ $order->status == 0 ? 'Pending' : ($order->status == 1 ? 'Completed' : ($order->status == 2 ? 'Canceled' : ($order->status == 3 ? 'Scheduled' : 'Live Now'))) }}</span>
                            <span class="text-danger">{{$order->is_edited==0?'(Note:- Please fill your detail)':''}}</span>
                    </div>
                    <div class="col-12 my-2">
                        @if ($order->status == 0 && $order->schedule_status == 0 && $order->is_edited==1)
                            <button class="btn btn-primary btn-sm"
                                onclick="scheduleModal('{{ $order->order_id }}')">Reschedule</button>
                        @endif
                        @if ($order->status == 0)
                            <button class="btn btn-danger btn-sm"
                                onclick="cancelModal('{{ $order->order_id }}')">Cancel</button>
                        @endif
                    </div>
                </div>

            </div>
            @if ($order->status == 0)
                @if ($order->remain_amount_status == 0)
                    <div class="col-md-6 d-flex justify-content-end align-items-baseline gap-2">
                        <p>Remaining Balance : <span
                                class="font-bold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->remain_amount), currencyCode: getCurrencyCode()) }}</span>
                        </p>
                        <button type="button" class="btn btn-success btn-sm" data-orderid="{{ $order->order_id }}"
                            data-customerid="{{ $order->customer_id }}"
                            onclick="remainingPayModal(this)">Pay</button>
                    </div>
                @endif
            @endif
        </div>

        {{-- <div class="date fs-12 font-semibold text-secondary-50 text-body mb-3 mt-2">
            {{ date('d M, Y h:i A', strtotime($order['created_at'])) }}
        </div> --}}
    </div>

    <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
                fill="white" />
        </svg>
    </button>
</div>

<div class="order-details-nav overflow-auto nav-menu gap-3 gap-xl-30 mb-4 text-capitalize d-flex">
    <button data-link="{{ route('account-offlinepooja-order-details', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-order-details') ? 'active' : '' }}">{{ translate('order_summary') }}</button>
    <button data-link="{{ route('account-offlinepooja-pandit-details', ['order_id' => $order['order_id']]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-pandit-details') ? 'active' : '' }}">{{ translate('Information') }}</button>
    <button data-link="{{ route('account-offlinepooja-order-track', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-order-track') ? 'active' : '' }}">{{ translate('track_Order') }}</button>
    <button data-link="{{ route('account-offlinepooja-certificate', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-certificate') ? 'active' : '' }}">{{ translate('Certificate_download') }}</button>
    <button data-link="{{ route('account-offlinepooja-sankalp', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-sankalp') ? 'active' : '' }}">{{ translate('sankalp_Details') }}</button>
    <button data-link="{{ route('account-offlinepooja-review', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-offlinepooja-review') ? 'active' : '' }}"
        @if ($order->status != 1) disabled @endif>{{ translate('reviews') }}</button>
</div>
