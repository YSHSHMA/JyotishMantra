<?php

namespace App\Listeners;

use App\Events\CustomerStatusUpdateEvent;
use App\Traits\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CustomerStatusUpdateListener
{
    use PushNotificationTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    public function handle(CustomerStatusUpdateEvent $event): void
    {
        $this->sendNotification($event);
    }
    /**
     * Handle the event.
     */

    public function sendNotification($event): void
    {
        $key = $event->key;
        $type = $event->type;
        $lang = $event->lang;
        $status = $event->status;
        $fcmToken = $event->fcmToken;
        $this->customerStatusUpdateNotification(key: $key, type: $type, lang: $lang, status: $status,fcmToken: $fcmToken);
    }
}
