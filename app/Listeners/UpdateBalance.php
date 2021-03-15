<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Transaction;
use App\Wallet;
use App\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmTransaction;

class UpdateBalance
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
     * @param  TransactionCreated  $event
     * @return void
     */
    public function handle(TransactionCreated $event)
    {

        $checkWallet = Wallet::where('customer_id', '=', $event->transaction->customer_id)
            ->first();
    
        if($event->transaction->type == 'credit') {
            $checkWallet->balance = $checkWallet->balance + $event->transaction->mount;
        }
        else {
            if($checkWallet->balance - $event->transaction->mount >= 0) {
                $checkWallet->balance = $checkWallet->balance - $event->transaction->mount;
            }

            $token = bin2hex(random_bytes((6 - (6 % 2)) / 2));

            Mail::to('alzolarma@gmail.com')->send(new ConfirmTransaction($token));
        }

        $checkWallet->save();

    }
}
