<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapController;
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

Route::get('/balance', function () {
    $wsdl = "http://127.0.0.1:8000/index.php/api?wsdl";
    // Create client object
     $client = new \nusoap_client($wsdl);

     // return array('message' => 'Mensaje resultado', 'code' =>   $client);


    //  $client->soap_defencoding = 'UTF-8';
    //  $client->decode_utf8 = FALSE;

    $err = $client->getError();
    if ($err) {
     return array('message' => 'getError', 'code' =>  200);
    }

    $param = array('phone' => '909090', 'document' => '909090');
    $result = $client->call('getBalance', $param, '', '', false, true);

    if ($client->fault) {
        return array('message' => 'Mensaje fault', 'code' =>  $result);
    } else {
        // Check for errors
        $err = $client->getError();
        if ($err) {
            // Display the error
        return array('message' => 'Mensaje err', 'code' =>  $err);
        } else {
            // Display the result
            echo '<h2>Result</h2><pre>';
            return array('message' => 'Mensaje Result', 'code' =>  $result);
            echo '</pre>';
        }
    }

    return view('welcome');
});

Route::any('api', function() {

    $server = new \nusoap_server();
    // Define our namespace
    $namespace = "http://127.0.0.1:8000";
    // Configure our WSDL
    $server->configureWSDL("WalletTest");
    // Initialize WSDL support
    $server->configureWSDL('api', 'urn:api');

    $server->wsdl->addComplexType(
        'GetBalance',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'phone' => array('name' => 'phone', 'type' => 'xsd:string'),
            'document' => array('name' => 'document', 'type' => 'xsd:int')
        )
    );

    $server->wsdl->addComplexType(
        'CustomerStore',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'phone' => array('name' => 'phone', 'type' => 'xsd:string'),
            'document' => array('name' => 'document', 'type' => 'xsd:int'),
            'email' => array('name' => 'email', 'type' => 'xsd:string'),
            'name' => array('name' => 'name', 'type' => 'xsd:string'),
        )
    );

    $server->wsdl->addComplexType(
        'SweepstakesGreeting',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'greeting' => array('name' => 'greeting', 'type' => 'xsd:string'),
            'winner' => array('name' => 'winner', 'type' => 'xsd:boolean')
        )
    );

    $server->wsdl->addComplexType(
        'Response',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'message' => array('name' => 'message', 'type' => 'xsd:string'),
            'status' => array('name' => 'status', 'type' => 'xsd:boolean'),
            'data' => array('name' => 'data', 'type' => 'xsd:json'),
            'errors' => array('name' => 'errors', 'type' => 'xsd:string')
        )
    );

    // Register methods

    $server->register('getBalance',                    // method name
        array('getBalance' => 'tns:GetBalance'),          // input parameters
        array('return' => 'tns:Response'),    // output parameters
        'urn:api',                         // namespace
        'urn:api#hello',                   // soapaction
        'rpc',                                    // style
        'encoded',                                // use
        'Greet a person entering the sweepstakes'        // documentation
    );

    $server->register('customerStore',
        array('customerStore' => 'tns:CustomerStore'),
        array('return' => 'tns:Response'),
        'urn:api',
        'urn:api#hello',
        'rpc',
        'encoded',
        'Greet a person entering the sweepstakes'
    );

    $server->register('InsertData',
    array(
        'data' => 'xsd:data',       
        'data1' => 'xsd:data1',
        'data2'  => 'xsd:data2',
        'data3'  => 'xsd:data3',
        'data4' => 'xsd:data4',
    ),
    array(
        'return' => 'xsd:string'        
    ),
    'urn:api',
    'urn:api#InsertData',
    'rpc',
    'encoded',
    'Retrieve data from  the database'
    );

    function getBalance($request) {
        $soap = new SoapController();
        $response = $soap->getBalance($request);
        return $response;
    }

    function customerStore($request) {
        $soap = new SoapController();
        $response = $soap->customerStore($request);
        return $response;
    }

    function InsertData($data, $data1, $data2, $data3, $data4){
return array(
                'message' => 'Registro creado',
                'status' => true,
            );    }

    // $data = json_decode(file_get_contents("php://input"), true);

    
    $POST_DATA = file_get_contents("php://input");
    // return $POST_DATA;

    // $POST_DATA =  '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:api">
    // <soapenv:Header/>
    // <soapenv:Body>
    //     <urn:getBalance soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    //         <getBalance xsi:type="urn:GetBalance">
    //             <!--You may enter the following 2 items in any order-->
    //             <phone xsi:type="xsd:string">909090</phone>
    //             <document xsi:type="xsd:int">909090</document>
    //         </getBalance>
    //     </urn:getBalance>
    // </soapenv:Body>
    // </soapenv:Envelope>';

    return \Response::make($server->service($POST_DATA), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});