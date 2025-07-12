<?php
$allowed_origin = 'https://trendmart-store.netlify.app';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * 
 */
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../core/Database.php';
include 'general_function.php';
class Main_API
{
	
	function __construct()
	{
		global $env_mysql_db_host, $env_mysql_db_user, $env_mysql_db_pass, $env_mysql_db_name;
		try {
		    $dsn = "pgsql:host=db.hlxpmuuhswynspjuihos.supabase.co;port=5432;dbname=postgres";
		    $pdo = new PDO($dsn, 'postgres', 'trendmart-db');
		    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    echo "Connected successfully!";
		} catch (PDOException $e) {
		    echo "Connection failed: " . $e->getMessage();
		}
		exit;
		$this->db_conn = new Database($env_mysql_db_host, $env_mysql_db_user, $env_mysql_db_pass, $env_mysql_db_name);
		$this->glob = $_POST;
	}
	public function api_function($api_name)
	{
		$login_api = ['get_login_details','register_user'];
		if(in_array($api_name, $login_api)){
			include 'login_api.php';
			$login_api = new Login_API();
			return $login_api->$api_name();
		}
		$seller_api = ['get_category_details','add_product','get_product_details','delete_product','get_single_product_detail','add_to_cart','get_cart_product_details'];
		if(in_array($api_name, $seller_api)){
			include 'seller_customer_api.php';
			$seller_api = new Seller_API();
			return $seller_api->$api_name();
		}
	}
}

$api = new Main_API();
if(isset($_POST['api'])){
	$data = $api->api_function($_POST['api']);
	header('Content-Type: application/json');
	echo json_encode($data);
	exit;
}else{
	http_response_code(404);
	echo json_encode(['error' => 'API not specified']);
}
?>
