<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $table = 'payment_requests';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
