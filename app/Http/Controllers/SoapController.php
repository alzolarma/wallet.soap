<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Builder;

use App\Soap;
use App\Customer;
use App\Transaction;
use App\Wallet;
use Illuminate\Http\Request;
use App\Events\TransactionCreated;

class SoapController extends Controller
{

    function getBalance($request) {
        try {
            $customer = Customer::where('phone', '=', $request['phone'])
                        ->where('document', '=', $request['document'])
                        ->first()->balance;
            if($customer) {
                return array(
                    'message' => 'Consulta exitosa',
                    'status' => true,
                    'code' => 200,
                    'data' =>  $customer->balance,
                    'errors' => null
                );
            }
        } catch (\Throwable $th) {
            return array(
            'message' => 'Usuario no encontrado',
            'status' => false,
            'data' => null,
            'code' => 404,
            'errors' => null
            );
        }
    }

    function customerStore($request) {
        try {

            $checkCustomer = Customer::where('phone', '=', $request['phone'])
            ->where('document', '=', $request['document'])
            ->first();

            if ($checkCustomer) {
                return array(
                    'message' => 'Registro ya existe en base de datos',
                    'status' => false,
                    'data' => null,
                    'code' => 202,
                    'errors' => null
                );
            }

            $customer = new Customer();
            $customer->name = $request['name'];
            $customer->phone = $request['phone'];
            $customer->document = $request['document'];
            $customer->email = $request['email'];
            $customer->save();

            $wallet = new Wallet();
            $wallet->balance = 0;
            $wallet->customer_id = $customer->id;
            $wallet->save();

            return array(
                'message' => 'Registro creado',
                'code' => 200,
                'status' => true,
                'errors' => null,
                'data' => null,
            );

        } catch (\Throwable $th) {
           return array(
            'message' => 'Ha ocurrido un error',
            'status' => false,
            'data' => null,
            'code' => 500,
            'errors' => null
            );
        }
    }

    function transactionStore($request) {
        try {

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

            $transaction = new Transaction();
            $transaction->type = $request['type'];
            $transaction->mount = $request['mount'];
            $transaction->customer_id = $checkCustomer->id;
            $transaction->save();

            TransactionCreated::dispatch($transaction);

            return array(
                'message' =>  'Transacción realizada',
                'code' => 200,
                'status' => true,
                'errors' => null,
                'data' => null,
            );

        } catch (\Throwable $th) {
           return array(
            'message' => 'Ha ocurrido un error'.$th,
            'status' => false,
            'data' => null,
            'code' => 500,
            'errors' => null
            );
        }
    }

}
