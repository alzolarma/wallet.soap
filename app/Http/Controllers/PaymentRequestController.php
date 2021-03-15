<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\PaymentRequest;
use App\Transaction;
use App\Customer;
use App\Wallet;
use App\Events\PaymentRequestCreated;
use App\Events\TransactionCreated;

class PaymentRequestController extends Controller
{
    /**
     * Create new PaymentRequest.
     *
     * @param  string  $name
     * @param  string  $document
     * @param  string  $mount
     * @return array
     */
    function store($request) {

        $checkCustomer = Customer::where('phone', '=', $request['phone'])
            ->where('document', '=', $request['document'])
            ->first();

        if (!$checkCustomer) {
            return array(
                'message' => 'Registro no existe en base de datos',
                'status' => false,
                'data' => null,
                'code' => 202,
                'errors' => null
            );
        }

        if($checkCustomer->balance->balance < $request['mount']) {
            return array(
                'message' =>  'Saldo insuficiente',
                'code' => 202,
                'status' => false,
                'errors' => null,
                'data' => null,
            );
        }

        $token = bin2hex(random_bytes((6 - (6 % 2)) / 2));

        $paymentRequest = new PaymentRequest();
        $paymentRequest->mount = $request['mount'];
        $paymentRequest->token = $token;
        $paymentRequest->customer_id = $checkCustomer->id;
        $paymentRequest->save();


        PaymentRequestCreated::dispatch($paymentRequest);

        return array(
            'message' =>  'Revise su correo para confirmar la transacción',
            'code' => 200,
            'status' => true,
            'errors' => null,
            'data' => null,
        );
    }

     /**
     * Confirma a PaymentRequest.
     *
     * @param  string  $token
     * @param  string  $sesion
     * @return array
     */
    function confirm($request) {

        $paymentRequest = PaymentRequest::where('token', '=', $request['token'])->first();

        if (!$paymentRequest) {
            return array(
                'message' => 'Token caducado o ya no existe',
                'status' => false,
                'data' => null,
                'code' => 404,
                'errors' => null
            );
        }

        if( $paymentRequest['mount'] > $paymentRequest->customer->balance->balance) {
            return array(
                'message' =>  'Saldo insuficiente',
                'code' => 202,
                'status' => false,
                'errors' => null,
                'data' => null,
            );
        }

        $paymentRequest->token = null;
        $paymentRequest->save();

        $transaction = new Transaction();
        $transaction->type = 'debit';
        $transaction->mount = $paymentRequest->mount;
        $transaction->customer_id = $paymentRequest->customer_id;
        $transaction->save();

        TransactionCreated::dispatch($transaction);

        return array(
            'message' =>  'Transacción realizada',
            'code' => 200,
            'status' => true,
            'errors' => null,
            'data' => null,
        );

    }
}