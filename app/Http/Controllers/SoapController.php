<?php

namespace App\Http\Controllers;

use App\Soap;
use Illuminate\Http\Request;

class SoapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $server = new \nusoap_server();
        $namespace = "soapwallet";
        //$server->configureWSDL('TestService', false, url('api'));
        $server->configureWSDL('SoapWalletService', $namespace);
        $server->wsdl->schemaTargetNamespace =  $namespace;

        $server->wsdl->addComplexType( 
            'createCustomer',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'name' => array('name' => 'name','type' => 'xsd:string'),
                'document' => array('name' => 'document','type' => 'xsd:string'),
                'email' => array('name' => 'email','type' => 'xsd:string'),
                'phone' => array('name' => 'phone','type' => 'xsd:string')
            )
        );

         $server->wsdl->addComplexType( 
            'response',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'code' => array('code' => 'name','type' => 'xsd:integer'),
                'message' => array('name' => 'message','type' => 'xsd:string'),
            )
        );

        $server->register('customerStore',
            array('name' => 'tns:createCustomer'),
            array('name' => 'tns:response'),
            $namespace,
            'rcp',
            'encoded',
            'Recibe orden para crear cliente y retorna codigo y mensaje'

        );

         $server->register('test',
            array(
              'name' => 'xsd:string',
            ),
            array(
              'output' => 'xsd:string',
            ),
            $namespace,
            'rcp',
            'encoded',
            'Recibe orden para crear cliente y retorna codigo y mensaje'
        );

        // $server->register('customer',
        //     array(
        //         'name' => 'xsd:string',
        //         'document' => 'xsd:string',
        //         'email' => 'xsd:string',
        //         'phone' => 'xsd:string'
        //     ),
        //     array(
        //          'code' => 'xsd:string',
        //          'message' => 'xsd:string'
        //     ),
        //     false,
        //     false,
        //     false,
        //     false,
        //     'Crear nuevo cliente'
        // );

        $rawPostData = file_get_contents("php://input");

        return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
    }

    function test($input){
        return "Resultado final ".$input;
    }

    function createCustomer(){
        $url = "http://ws.cdyne.com/ip2geo/ip2geo.asmx?wsdl";
        //$client = new \nusoap_client($url, [ "trace" => 1 ] );
        $client = new \nusoap_client('http://127.0.0.1:8000/index.php?wsdl', true);
        $result = $client->call('ResolveIP', [ "ipAddress" => '0000', "licenseKey" => "0" ]);
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($request)
    {
     return array('message' => 'Mensaje resultado', 'code' =>  200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function show(Soap $soap)
    {
        // $url = "http://ws.cdyne.com/ip2geo/ip2geo.asmx?wsdl";
        // $client = new \nusoap_client($url, true);
        // $result = $client->call('ResolveIP', [ "ipAddress" => '0000', "licenseKey" => "0" ]);

         $url = "http://127.0.0.1:8000/walletapi?wsdl";
         $client = new \nusoap_client($url, true);
         $result = $client->call('test', 'Maria');
        
        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function edit(Soap $soap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Soap $soap)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function destroy(Soap $soap)
    {
        //
    }
}
