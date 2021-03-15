<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    /**
     * Get the balance associated with the customer.
     */
    public function balance()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the balance associated with the customer.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class)->select('balance');
    }

    /**
     * Get the paymentRequest associated with the customer.
     */
    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class, 'customer_id');
    }

}
