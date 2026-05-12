<?php

namespace App\Providers;

use App\Events\AddFundToWalletEvent;
use App\Events\CashCollectEvent;
use App\Events\CustomerStatusUpdateEvent;
use App\Events\DeliverymanPasswordResetEvent;
use App\Events\DigitalProductOtpVerificationMailEvent;
use App\Events\EmailVerificationEvent;
use App\Events\OrderPlacedEvent;
use App\Events\PasswordResetMailEvent;
use App\Events\ChattingEvent;
use App\Events\OrderStatusEvent;
use App\Events\ProductRequestStatusUpdateEvent;
use App\Events\RefundEvent;
use App\Events\VendorRegistrationMailEvent;
use App\Events\VendorStatusUpdateEvent;
use App\Events\WithdrawStatusUpdateEvent;
use App\Listeners\AddFundToWalletListener;
use App\Listeners\CashCollectListener;
use App\Listeners\CustomerStatusUpdateListener;
use App\Listeners\DeliverymanPasswordResetListener;
use App\Listeners\DigitalProductOtpVerificationMailListener;
use App\Listeners\EmailVerificationListener;
use App\Listeners\OrderPlacedListener;
use App\Listeners\PasswordResetMailListener;
use App\Listeners\ChattingListener;
use App\Listeners\OrderStatusListener;
use App\Listeners\ProductRequestStatusUpdateListener;
use App\Listeners\RefundListener;
use App\Listeners\VendorRegistrationMailListener;
use App\Listeners\VendorStatusUpdateListener;
use App\Listeners\WithdrawStatusUpdateListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        AddFundToWalletEvent::class => [
            AddFundToWalletListener::class,
        ],
        DigitalProductOtpVerificationMailEvent::class => [
            DigitalProductOtpVerificationMailListener::class,
        ],
        DeliverymanPasswordResetEvent::class => [
            DeliverymanPasswordResetListener::class,
        ],
        EmailVerificationEvent::class => [
            EmailVerificationListener::class,
        ],
        PasswordResetMailEvent::class => [
            PasswordResetMailListener::class,
        ],
        OrderPlacedEvent::class => [
            OrderPlacedListener::class,
        ],
        OrderStatusEvent::class => [
            OrderStatusListener::class,
        ],
        ChattingEvent::class => [
            ChattingListener::class,
        ],
        RefundEvent::class => [
            RefundListener::class,
        ],
        VendorRegistrationMailEvent::class => [
            VendorRegistrationMailListener::class,
        ],
        CustomerStatusUpdateEvent::class => [
            CustomerStatusUpdateListener::class,
        ],
        WithdrawStatusUpdateEvent::class => [
            WithdrawStatusUpdateListener::class,
        ],
        CashCollectEvent::class => [
            CashCollectListener::class,
        ],
        ProductRequestStatusUpdateEvent::class => [
            ProductRequestStatusUpdateListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
