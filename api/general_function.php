<?php
/*MAKE MYSQL DB CONNECTION*/
function db_connection()
{
	require_once __DIR__ . '/../env.php';
	$conn = new mysqli($env_mysql_db_host, $env_mysql_db_user, $env_mysql_db_pass, $env_mysql_db_name);
	if ($conn->connect_error) {
	    die('Database connection failed: ' . $conn->connect_error);
	}
	return $conn;
}
/*TO ENCRYPT AND DECRYPT ID*/
function encrypt_decrypt($string, $action = 'e') {
    $encryption_key = 'a3f9d91e6b9d4f3dbf1c4d2e8b7f9d10';
    $method = 'AES-256-CBC';
    $key = hash('sha256', $encryption_key);
    $iv = substr(hash('sha256', 'iv-secret'), 0, 16);

    if ($action === 'e') {
        return base64_encode(openssl_encrypt($string, $method, $key, 0, $iv));
    } elseif ($action === 'd') {
        return openssl_decrypt(base64_decode($string), $method, $key, 0, $iv);
    } else {
        return false;
    }
}
?>