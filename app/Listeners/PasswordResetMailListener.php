<?php

namespace App\Listeners;

use App\Events\PasswordResetMailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class PasswordResetMailListener
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
    public function handle(PasswordResetMailEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(PasswordResetMailEvent $event):void{
        $url = $event->url;
        $email = $event->email;
        try{
            Mail::to($email)->send(new \App\Mail\PasswordResetMail($url));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
