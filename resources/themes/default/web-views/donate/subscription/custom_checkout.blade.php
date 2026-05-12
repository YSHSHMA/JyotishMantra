@extends('payment.layouts.master')

@section('content')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script type="text/javascript">
        "use strict";
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                key: "{{ $RAZORPAY_KEY }}",               // Razorpay Key
                subscription_id: "{{ $subscription_id }}", // Subscription ID
                name: "{{ $business_name }}",           // Business Name
                image: "{{ asset('public/assets/front-end/img/mahakal-logo.gif') }}", // Logo
                prefill: {
                    name: "{{ $customername ?? '' }}",
                    email: "{{ $customeremail ?? '' }}",
                    contact: "{{ $customerphone ?? '' }}"
                },
                theme: {
                    color: "#ff7529"
                },
                handler: function (response) {
                    // On success -> send to success route
                    window.location.href = "{{ route('donate-success', [$lead_id]) }}"
                },
                modal: {
                    ondismiss: function () {
                        // On dismiss -> send to fail route
                        window.location.href = "{{ url('/') }}";
                    }
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();

            // Extra safety: handle failure
            rzp.on('payment.failed', function () {
                window.location.href = "{{ url('/') }}";
            });
        });
    </script>
@endsection
