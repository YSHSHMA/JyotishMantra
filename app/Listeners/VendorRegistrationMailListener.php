<?php

namespace App\Listeners;

use App\Events\VendorRegistrationMailEvent;
use App\Mail\VendorRegistrationMail;
use Illuminate\Support\Facades\Mail;

class VendorRegistrationMailListener
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
    public function handle(VendorRegistrationMailEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(VendorRegistrationMailEvent $event):void{
        $email = $event->email;
        $data = $event->data;
        try{
            Mail::to($email)->send(new VendorRegistrationMail($data));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
