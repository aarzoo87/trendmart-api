<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}
$env_mysql_db_host = 'localhost';
$env_mysql_db_user = 'root';
$env_mysql_db_pass = '';
$env_mysql_db_name = 'trendmart_db';
?>