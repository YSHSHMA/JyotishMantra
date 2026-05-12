@if ($digital_payment['status'] == 1)
    @foreach ($payment_gateways_list as $payment_gateway)
        <form method="post" class="digital_payment offlinepooj-pending-form" id="{{ $payment_gateway->key_name }}_form"
            action="{{ route('admin.counselling.order.pending.payment.request') }}">
            @csrf

            <div class="Details">
                <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                <input type="hidden" name="payment_platform" value="web">
                @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->live_values['callback_url'] }}">
                @elseif($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                @else
                    <input type="hidden" name="callback" value="">
                @endif
                <input type="hidden" name="external_redirect_link"
                    value="{{ url('/') . '/counselling-pending-web-payment' }}">
                <label class="d-flex align-items-center gap-2 mb-0 form-check cursor-pointer">
                    <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment"
                        class="form-check-input custom-radio" value="{{ $payment_gateway->key_name }}" hidden>
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
                <input type="hidden" name="order_id" id="pending-order-id" value="">

                {{-- <button type="submit" class="btn btn-md btn-primary">Pay</button> --}}
            </div>
            {{-- <div class="mt-4">
            </div> --}}

        </form>
    @endforeach
@endif
