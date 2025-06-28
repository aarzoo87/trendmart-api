<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}
$env_mysql_db_host = 'sql12.freesqldatabase.com';
$env_mysql_db_user = 'sql12787303';
$env_mysql_db_pass = 'XbCik6ibce';
$env_mysql_db_name = 'sql12787303';
?>