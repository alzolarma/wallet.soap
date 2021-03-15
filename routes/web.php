<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapController;
use App\Http\Controllers\PaymentRequestController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('api', function() {

    $server = new \nusoap_server();

    //Define our namespace
    $namespace = "http://127.0.0.1:8000";
    //Configure our WSDL
    $server->configureWSDL("WalletTest");
    $server->configureWSDL('api', 'urn:api');

    // Register the methods
    $server->register('GET_BALANCE',
        array(
            'phone' => 'xsd:string',
            'document' => 'xsd:string',
        ),
        array(
            'message' => 'xsd:string',
            'data' => 'xsd:string',
            'code' => 'xsd:string',
            'errors' => 'xsd:string',
            'status' => 'xsd:boolean',
        ),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Devuelve el saldo del cliente'
    );

    $server->register('STORE_CUSTOMER',
        array(
            'name' => 'xsd:string',
            'phone' => 'xsd:string',
            'document' => 'xsd:string',
            'email' => 'xsd:string',
        ),
        array(
            'message' => 'xsd:string',
            'data' => 'xsd:string',
            'code' => 'xsd:string',
            'errors' => 'xsd:string',
            'status' => 'xsd:boolean',
        ),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Crea un nuevo usuario'
    );

    $server->register('MAKE_TRANSACTION',
        array(
            'phone' => 'xsd:string',
            'document' => 'xsd:string',
            'type' => 'xsd:string',
            'mount' => 'xsd:string',
        ),
        array(
            'message' => 'xsd:string',
            'data' => 'xsd:string',
            'code' => 'xsd:string',
            'errors' => 'xsd:string',
            'status' => 'xsd:boolean',
        ),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Realizar una transaccion de credito'
    );

    $server->register('PAYMENT_REQUEST',
        array(
            'phone' => 'xsd:string',
            'document' => 'xsd:string',
            'mount' => 'xsd:string',
        ),
        array(
            'message' => 'xsd:string',
            'data' => 'xsd:string',
            'code' => 'xsd:string',
            'errors' => 'xsd:string',
            'status' => 'xsd:boolean',
        ),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Realiza una solicitud de pago'
    );

     $server->register('CONFIRM_PAYMENT',
        array(
            'token' => 'xsd:string',
        ),
        array(
            'message' => 'xsd:string',
            'data' => 'xsd:string',
            'code' => 'xsd:string',
            'errors' => 'xsd:string',
            'status' => 'xsd:boolean',
        ),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Confirma un pago'
    );

    function GET_BALANCE($phone, $document) {
        $request = array('phone'=>$phone,'document'=>$document);
        $soap = new SoapController();
        $response = $soap->getBalance($request);
        return $response;
    }

    function STORE_CUSTOMER($name, $phone, $document, $email) {
        $request = array('name'=>$name,'phone'=>$phone,'document'=>$document,'email'=>$email);
        $soap = new SoapController();
        $response = $soap->customerStore($request);
        return $response;
    }

    function MAKE_TRANSACTION($phone, $document, $type, $mount) {
        $request = array('phone'=>$phone,'document'=>$document,'mount'=>$mount,'type'=>$type);
        $soap = new SoapController();
        $response = $soap->transactionStore($request);
        return $response;
    }

    function PAYMENT_REQUEST($phone, $document, $mount) {
        $request = array('phone'=>$phone,'document'=>$document,'mount'=>$mount);
        $paymentRequestController = new PaymentRequestController();
        $response = $paymentRequestController->store($request);
        return $response;
    }

    function CONFIRM_PAYMENT($token) {
        $request = array('token'=>$token);
        $paymentRequestController = new PaymentRequestController();
        $response = $paymentRequestController->confirm($request);
        return $response;
    }

    $POST_DATA = file_get_contents("php://input");

    return \Response::make($server->service($POST_DATA), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});