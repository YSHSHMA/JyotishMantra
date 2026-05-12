<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Traits\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class OrderPlacedListener
{
    use PushNotificationTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlacedEvent $event): void
    {
        if ($event->emailInfo) {
            $this->sendMail($event);
        }
        if ($event->notification) {
            $this->sendNotification($event);
        }
    }

    private function sendMail(OrderPlacedEvent $event): void
    {
        $orderId = $event->emailInfo->orderId;
        $email = $event->emailInfo->email;
        try {
            // Mail::to($email)->send(new \App\Mail\OrderPlaced($orderId));
        } catch (\Exception $exception) {
            info($exception);
        }
    }

    private function sendNotification(OrderPlacedEvent $event): void
    {
        $key = $event->notification->key;
        $type = $event->notification->type;
        $order = $event->notification->order;
        $this->sendOrderNotification(key: $key, type: $type, order: $order);
    }
}
