<?php
function db_connection()
{
	require_once __DIR__ . '/../env.php';
	$conn = new mysqli($env_mysql_db_host, $env_mysql_db_user, $env_mysql_db_pass, $env_mysql_db_name);
	if ($conn->connect_error) {
	    die('Database connection failed: ' . $conn->connect_error);
	}
	return $conn;
}
?>