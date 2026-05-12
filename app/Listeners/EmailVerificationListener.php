<?php

namespace App\Listeners;

use App\Events\EmailVerificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EmailVerificationListener
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
    public function handle(EmailVerificationEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(EmailVerificationEvent $event):void{
        $token = $event->token;
        $email = $event->email;
        try{
            Mail::to($email)->send(new \App\Mail\EmailVerification($token));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
