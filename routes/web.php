<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapController;
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

// Route::post('customer', [SoapController::class, 'store']);
// Route::get('customer', [SoapController::class, 'create']);
// Route::get('createCustomer', [SoapController::class, 'createCustomer']);


$server = new \nusoap_server();
$namespace = "SoapWalletService";
$url = "http://127.0.0.1:8000";

$server->configureWSDL('http://127.0.0.1:8000/apirest?wsdl', url('api'));
//$server->configureWSDL('SoapWalletService', $namespace, $url);
$server->wsdl->schemaTargetNamespace =  $namespace;

$server->register('test',
    array('input' => 'xsd:string'),
    array('output' => 'xsd:string')
);


Route::any('api', function() {
    $server = new \nusoap_server();
    $url = "http://127.0.0.1:8000";
    $server->configureWSDL('SoapWalletService', false, url('api'));

    $server->register('test',
        array('input' => 'xsd:string'),
        array('output' => 'xsd:string')
    );

    $server->register('customer',
        array(
            'name' => 'xsd:string',
            'document' => 'xsd:string',
            'email' => 'xsd:string',
            'phone' => 'xsd:string'
        ),
        array(
                'code' => 'xsd:string',
                'message' => 'xsd:string'
        ),
        false,
        false,
        false,
        false,
        'Crear nuevo cliente'
    );

    function test($input){
        return $input;
    }

    $rawPostData = file_get_contents("php://input");
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});