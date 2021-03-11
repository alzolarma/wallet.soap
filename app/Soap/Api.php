<?php

require_once('./Client.php');

if(!isset($_REQUEST['op'])) {
    die("opertion name required");
}

if(!method_exists($client, $_REQUEST['op']) or !in_array($_REQUEST['op'], ['getAll', 'getById'])) {
    die("invalid operation name");
}

switch ($_REQUEST['op']) {
    case 'getAll':

        $products = $client->getAll();

        echo "<pre>";
        print_r($products);
        echo "</pre>";
        break;

    case 'getById': 

        if(!isset($_REQUEST['id'])) {
            die("id parameter required");
        }

        $product = $client->getById(['id' => $_REQUEST['id']]);

        echo "<pre>";
        print_r($product);
        echo "</pre>";
        break;
    
    default:
        
        break;
}