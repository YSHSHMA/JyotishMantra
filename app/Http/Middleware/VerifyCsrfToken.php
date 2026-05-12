<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;


class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/pay-via-ajax',
        '/success',
        '/cancel',
        '/fail',
        '/ipn',
        '/bkash/*',
        '/paytabs-response',
        '/customer/choose-shipping-address',
        '/system_settings',
        '/paytm*',
        'razorpay/webhook',
        'razorpay/*',
        '/trustees-vendor/recepit-management/get-order-details-puja-slip',
        '/trustees-vendor/order-management/store-pooja',
        '/donate-leads/*',
        '/donate-lead-update',
        "/tour/tour-booking-tab",
        "/tour-payment-request/*",
    ];
}
