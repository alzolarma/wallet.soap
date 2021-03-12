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


Route::any('api', function() {
    $server = new \nusoap_server();
    $url = "http://127.0.0.1:8000";
    $server->configureWSDL('SoapWalletService', false, url('api'));

    $server->wsdl->addComplexType( 
            'response',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'message' => array('name' => 'message','type' => 'xsd:string'),
                'code' => array('name' => 'code','type' => 'xsd:string')
            )
    );

    $server->wsdl->addComplexType( 
            'requestCustomer',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'name' => array('name' => 'name','type' => 'xsd:string'),
                'phone' => array('name' => 'phone','type' => 'xsd:string')
            )
    );

    $server->register('test',
        array(
            'name' => 'xsd:string',
            'phone' => 'xsd:string',
            'document' => 'xsd:string',
            'email' => 'xsd:string'
        ),
        array('output' => 'xsd:string')
    );

    $server->register(
        'customer',
        array('tns:requestCustomer'),
        array('tns:response'),
        false,
        false,
        'rcp',
        'encoded',
        'Recibe una orden'
    );

    function test($request) {
        $soapController = new SoapController();
        $result = $soapController->test($request);
        return $result;
    }

    function customer($request) {
        $soapController = new SoapController();
        $result = $soapController->store($request);
        return  $result;
    }

    $rawPostData = file_get_contents("php://input");
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});