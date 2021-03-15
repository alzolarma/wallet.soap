<?php

namespace App\Listeners;

use App\Events\TransactionPending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Transaction;
use App\Wallet;
use App\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmTransaction;

class SendEmailConfirmation
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
     * @param  TransactionPending  $event
     * @return void
     */
    public function handle(TransactionPending $event)
    {
        $token = bin2hex(random_bytes((6 - (6 % 2)) / 2));
        $checkCustomer = Customer::where('id', '=',  $event->customer->id)
            ->first();
        $checkCustomer->token = $token;
        $checkCustomer->save();
        Mail::to($checkCustomer->email)->send(new ConfirmTransaction($token));
    }
}
