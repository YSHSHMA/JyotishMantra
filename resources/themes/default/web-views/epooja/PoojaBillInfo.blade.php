@extends('layouts.front-end.app')

@section('title', translate('order_Complete'))

@section('content')
    <div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card">
                    @if (auth('customer')->check() || session('guest_id'))
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                            </div>

                            <h6 class="font-black fw-bold text-center">
                                {{ translate('🎉 Your_order_has_been_successfully_completed!') }}
                            </h6>

                            @if (isset($sankalpData) > 0)
                                <p class="text-center fs-12">
                                    {{ translate('Your_payment_was_successful_and_your_order!') }} (
                                    <span class="fw-bold text-primary">{{ $sankalpData['order_id'] }}

                                    </span> )
                                    {{ translate('has_been_placed.') }}
                                </p>
                            @else
                                <p class="text-center fs-12">
                                    {{ translate('your_order_is_being_processed_and_will_be_completed.') }}
                                    {{ translate('You_will_receive_an_email_confirmation_when_your_order_is_placed.') }}
                                </p>
                            @endif
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ auth('customer')->check() ? route('account-service-order-details', ['order_id' => $sankalpData['order_id']]) : route('customer.auth.login') }}"
                                        class="btn btn--primary mb-3 text-center">{{ translate('View_details') }}</a>

                                </div>
                                <div class="col-12 text-center">
                                    <a href="{{ route('all-puja') }}" class="text-center">
                                        {{ translate('View_other_poojas') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script type="text/javascript">
        // document.addEventListener('DOMContentLoaded', function() {
        //     history.pushState(null, null, location.href);
        //     window.addEventListener('popstate', function(event) {
        //         history.pushState(null, null, location.href);
        //     });

        //     setTimeout(function() {
        //         window.location.replace(window.location.href);
        //     }, 0);
        // });

        // window.onpageshow = function(event) {
        //     if (event.persisted) {
        //         window.location.replace(window.location.href);
        //     }
        // };
    </script>
@endpush
