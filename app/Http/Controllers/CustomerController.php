<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Create new customer.
     *
     * @param  string  $name
     * @param  string  $document
     * @param  string  $email
     * @param  string  $phone
     * @return \Illuminate\View\View
     */
    public function store($id)
    {
        return view('user.profile', [
            'user' => Customer::store($id)
        ]);
    }
}