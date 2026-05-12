<?php

namespace App\Listeners;

use App\Events\AddFundToWalletEvent;
use App\Events\DigitalProductOtpVerificationMailEvent;
use App\Mail\DigitalProductOtpVerificationMail;
use Illuminate\Support\Facades\Mail;

class DigitalProductOtpVerificationMailListener
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
    public function handle(DigitalProductOtpVerificationMailEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(DigitalProductOtpVerificationMailEvent $event):void{
        $token = $event->token;
        try{
            Mail::to($event->email)->send(new DigitalProductOtpVerificationMail($token));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
