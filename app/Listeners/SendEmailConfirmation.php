<?php

namespace App\Listeners;

use App\Events\PaymentRequestCreated;
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
     * @param  PaymentRequestCreated  $event
     * @return void
     */
    public function handle(PaymentRequestCreated $event)
    {
        $checkCustomer = Customer::where('id', '=',  $event->paymentRequest->customer_id)
            ->first();
        Mail::to($checkCustomer->email)->send(new ConfirmTransaction($event->paymentRequest->token));
    }
}
