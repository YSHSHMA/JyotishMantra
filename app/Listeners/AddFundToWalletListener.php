<?php

namespace App\Listeners;

use App\Events\AddFundToWalletEvent;
use App\Mail\AddFundToWallet;
use Illuminate\Support\Facades\Mail;

class AddFundToWalletListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param AddFundToWalletEvent $event
     * @return void
     */
    public function handle(AddFundToWalletEvent $event): void
    {
        $this->sendMail($event);
    }

    private function sendMail(AddFundToWalletEvent $event):void{
        $walletTransaction = $event->walletTransaction;
        try{
            Mail::to($event->email)->send(new AddFundToWallet($walletTransaction));
        }catch(\Exception $exception) {
            info($exception);
        }
    }
}
