<?php if (!defined('DATATABLES')) exit(); // Ensure being used in DataTables env.

// Enable error reporting for debugging (remove for production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('America/New_york');
require_once("../../config/config.php");


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Database user / pass
 */
$sql_details = array(
        "type" => "Mysql",  // Database type: "Mysql", "Postgres", "Sqlite" or "Sqlserver"
        "user" => DB_USER,       // Database user name
        "pass" => DB_PASSWORD,       // Database password
        "host" => DB_HOST,       // Database host
        "port" => "",       // Database connection port (can be left empty for default)
        "db"   => DB_NAME,       // Database name
        "dsn"  => ""        // PHP DSN extra information. Set as `charset=utf8` if you are using MySQL
);
