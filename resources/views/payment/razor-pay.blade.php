@extends('payment.layouts.master')

<style>
    @keyframes progressbar {
        0% {
            transform: translateX(-75%);
        }

        100% {
            transform: translateX(225%);
        }
    }
</style>


@section('content')
<div>
    <!-- Image Above Processing Text -->
    <img src="{{ theme_asset(path: 'public/assets/front-end/img/payment-logo.gif') }}" alt="Processing"
        style="margin-bottom: 10px; width:380px">

    <h1 style="font-size: 3rem; line-height: 1.67; margin-top: 0; margin-bottom: 0.5rem;">
        Processing...
    </h1>

    <!-- Loading Bar -->
    <div
        style="width: 100%; border-radius: 9999px; height: 4px; background: #f3f3f3; overflow: hidden; position: relative;">
        <div
            style="border-radius: 9999px; height: 100%; width: 40%; transform-origin: center; background: #0065d1; position: absolute; left: 0; animation: progressbar 1s ease-in-out alternate infinite;">
        </div>
    </div>

    <!-- Message for Users -->
    <h1 style="margin-top: 10px; font-size: 20px; color: #d9534f; font-weight: bold;">
        {{ 'Please do not refresh this page...' }}
    </h1>
</div>

<form action="{!! route('razor-pay.payment', ['payment_id' => $data->id]) !!}" id="form" method="POST">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="order_id" id="razorpay_payment_id">
    {{-- <input type="hidden" name="order_id" id="order_id"> --}}
    <button class="btn btn-block" id="pay-button" type="button" hidden>Pay
        {{ round($data->payment_amount, 2) . ' ' . $data->currency_code }}</button>
    @php
    $additionalData = json_decode($data['additional_data'], true);
    $orderId = $additionalData['order_id'] ?? null;
    @endphp
</form>
{{-- {{ dd($$data->payment_amount); }} --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
    "use strict";
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            key: "{{ config()->get('razor_config.api_key') }}",
            amount: "{{ round($data->payment_amount, 2) * 100 }}",
            currency: "{{ $data->currency_code }}",
            name: "{{ $business_name }}",
            description: "{{ $data->payment_amount }}",
            notes: {
                info: "{{ $data->attribute ?? '' }}",
                orderid: "{{ ($data->attribute_id??'') }}",
            },
            orderid: "{{ ($data->attribute_id??'') }}",
            // image: "{{ $business_logo }}",
            image: "{{ asset('public/assets/front-end/img/mahakal-logo.gif') }}",
            receipt: "{{ $orderId }}",
            prefill: {
                name: "{{ $payer->name ?? '' }}",
                email: "{{ $payer->email ?? '' }}",
                phone: "{{ $payer->phone ?? '' }}"
            },
            theme: {
                color: "#ff7529"
            },
            handler: function(response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                console.log(response);
                document.getElementById('form').submit();
            },
            modal: {
                ondismiss: function() {
                    window.location.href = "{{ url('payment-fail?payment_id=' . $data->id) }}";
                    // window.location.href = previousUrl;
                }
            }
        };
        var rzp = new Razorpay(options);

        rzp.open();
        rzp.on('payment.failed', function(response) {
            console.log(response);
            window.location.href = "{{ url('payment-fail?payment_id=' . $data->id) }}";
            // window.location.href = previousUrl;
        });
    });
</script>
@endsection