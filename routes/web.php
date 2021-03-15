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

Route::any('api', function() {

    $server = new \nusoap_server();

    //Define our namespace
    $namespace = "http://127.0.0.1:8000";
    //Configure our WSDL
    $server->configureWSDL("WalletTest");
    $server->configureWSDL('api', 'urn:api');

    // Register addComplexType
    $server->wsdl->addComplexType(
        'Person',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'firstname' => array('name' => 'firstname', 'type' => 'xsd:string'),
            'age' => array('name' => 'age', 'type' => 'xsd:int'),
            'gender' => array('name' => 'gender', 'type' => 'xsd:string')
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
            'status' => array('name' => 'status', 'type' => 'xsd:boolean'),
            'msg' => array('name' => 'msg', 'type' => 'xsd:string'),
            'data' => array('name' => 'data', 'type' => 'xsd:string'),
            'errors' => array('name' => 'errors', 'type' => 'xsd:string')
        )
    );

    // Register the methods
    $server->register('hello',                    // method name
        array('person' => 'tns:Person',),          // input parameters
        array('return' => 'tns:SweepstakesGreeting'),    // output parameters
        'urn:api',                         // namespace
        'urn:api#hello',                   // soapaction
        'rpc',                                    // style
        'encoded',                                // use
        'Greet a person entering the sweepstakes'        // documentation
    );

    // Register the methods
    $server->register('GET_BALANCE',                    // method name
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
        'urn:api',                         // namespace
        'urn:api#hello',                   // soapaction
        'rpc',                                    // style
        'encoded',                                // use
        'Greet a person entering the sweepstakes'        // documentation
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
        'Crear nuevo usuario'
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
        'Crear nuevo usuario'
    );

    // Define the method as a PHP function
    function hello($firstname, $age, $gender) {
        return array(
                    'greeting' => $firstname . $age,
                    'winner' => $gender
                    );
        $greeting = 'Hello, ' . $person['firstname'] .
                    '. It is nice to meet a ' . $person['age'] .
                    ' year old ' . $person['gender'] . '.';
        
        $winner = $person['firstname'] == 'Scott';

    }

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

    $POST_DATA = file_get_contents("php://input");

    return \Response::make($server->service($POST_DATA), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});