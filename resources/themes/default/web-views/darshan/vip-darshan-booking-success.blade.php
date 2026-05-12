@extends('layouts.front-end.app')
@section('title', translate('booking_success'))
@push('css_or_js')
<meta property="og:image"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


    .section-header {
        height: 200px;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .section-header img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .price-box {
        background-color: #fff3e0;
        border: 1px solid #ffcc80;
        padding: 15px;
        border-radius: 8px;
    }

    .badge-custom {
        background-color: #fbe9e7;
        color: #d84315;
        padding: 8px 12px;
        border-radius: 20px;
        margin: 4px;
        display: inline-block;
        font-size: 14px;
    }

    .btn-outline-primary:hover {
        background-color: var(--web-primary) !important;
        border-color: var(--web-primary) !important;
    }

    .slot-btn {
        margin: 4px;
    }

    .custom-continue-btn {
        background: linear-gradient(to right, #ff9900, #ffcc66);
        color: white;
        font-weight: bold;
        border: none;
        padding: 15px 98px;
        font-size: 16px;
        border-radius: 20px;
        box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .custom-continue-btn:hover {
        box-shadow: 3px 6px 12px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }
</style>
@endpush
@section('content')

<div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
    <div class="row d-flex justify-content-center align-items-center">
        <section class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                    </div>
                    <div class="text-center">
                        <small>{{ $getData['name'] ?? '' }}</small>
                    </div>
                    <h6 class="font-black fw-bold text-center">
                        {{ translate('Vip_darshan_booked_successfully') }} !
                    </h6>
                    <p class="text-center fs-12">
                        {{ translate('We_have_received_your_VIP_Darshan_booking._Thank_you_for_choosing_our_service.') }}
                    </p>
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('account-order-darshan') }}" class="btn btn--primary mb-3 text-center">
                                {{ translate('view_booking') }}
                            </a>
                        </div>
                        <div class="col-12 text-center">
                            <a href="{{ route('darshan') }}" class=" text-center">
                                {{ translate('continue') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>







@endsection
@push('script')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

<script>
    history.pushState(null, '', location.href);

    // Triggered when user hits the back button
    window.addEventListener('popstate', function (event) {
        history.pushState(null, '', location.href);
    });
</script>
@endpush