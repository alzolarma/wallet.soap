<?php
define("DB_HOST", "localhost");
define("DB_NAME", "wallet");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("TABLE", "products");
class server
{
    private $db_handle;
    public function __construct()
    {
        $this->connect();
    }
    private function connect()
    {
        try {
            $this->db_handle = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }
    public function getAllProducts()
    {
        $query = mysqli_query($this->db_handle, "SELECT * FROM " . TABLE);
        $products = [];
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            array_push($products, new Product($row['id'], $row['title'], $row['description'], $row['price']));
        }
        return $products;
    }
    public function getProduct($params)
    {
        $query = mysqli_query($this->db_handle, "SELECT * FROM " . TABLE . " WHERE id='{$params['id']}'");
        $row = mysqli_fetch_row($query);
        if($row) {
            return new Product($row[0], $row[1], $row[2], $row[3]);
        }
        return "no such product";
    }
}
class Product 
{
    public $id, $title, $description, $price;
    public function __construct($id, $title, $description, $price) 
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
    }
}
$params = array('uri' => 'http://localhost/soap/server.php');
$soapServer = new SoapServer(null, $params);
$soapServer->setClass('server');
$soapServer->handle();