<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}
$env_pgsql_db_host = 'dpg-d1mti0nfte5s73d3odi0-a';
$env_pgsql_db_user = 'trendmart';
$env_pgsql_db_pass = 'sGkNsNDm7ZewUYvaFttkgaxVPwsOXXGn';
$env_pgsql_db_name = 'trendmart_db';
$env_server_url = 'https://trendmart-api.onrender.com';
?>