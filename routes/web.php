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
    $server->configureWSDL('api', false, url('api'));

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

    $server->register(
        'getBalance',
        array('tns:requestCustomer'),        // input parameters
        array('type'=>'xsd:string'),      // output parameters
        'urn:api',                      // namespace
        'urn:api#hello',                // soapaction
        'rpc',                                // style
        'encoded',                            // use
        'Retorna balance'            // documentation
    );
    
    function getBalance($request) {
        return  $request;
    }

    $rawPostData = file_get_contents("php://input");
    //return array('message' => 'Mensaje resultado', 'code' => $rawPostData);
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});