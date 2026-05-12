<?php

namespace App\Listeners;

use App\Events\DeliverymanPasswordResetEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class DeliverymanPasswordResetListener
{
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
    public function handle(DeliverymanPasswordResetEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(DeliverymanPasswordResetEvent $event):void{
        $otp = $event->otp;
        $email = $event->email;
        try{
            Mail::to($email)->send(new \App\Mail\DeliverymanPasswordResetMail($otp));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
