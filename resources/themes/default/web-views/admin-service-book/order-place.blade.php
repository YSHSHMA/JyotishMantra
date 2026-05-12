@extends('layouts.front-end.app')

@section('title', translate('pandit/pooja Order Place'))

@section('content')
    <div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card">
                    {{-- @if (auth('customer')->check() || session('guest_id')) --}}
                        <div class="card-body">
                            
                            @if ($type == 'success')
                            <div class="mb-3 text-center">
                                <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                            </div>
                                <h6 class="font-black fw-bold text-center">
                                    {{ translate('pooja_Successfully_Booked') }}!
                                </h6>
                                <p class="text-center fs-12">
                                    {{ translate('Your_request_is_being_processed_and_will_be_completed_soon._You_will_receive_a_confirmation_via_email_and_WhatsApp_shortly.') }}
                                </p>
                            @else
                            <h6 class="font-black fw-bold text-center">
                                {{ translate('payment_failed') }}!
                            </h6>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ route('home') }}" class="text-center">
                                        {{ translate('Continue') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection
