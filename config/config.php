<?php
 
/** Configuration Variables **/
 
define ('DEVELOPMENT_ENVIRONMENT',true);

date_default_timezone_set('America/New_York');


define('DB_NAME', 'biocore');
define('DB_USER', 'bioinfo');
define('DB_PASSWORD', 'bioinfo2013');
#define('DB_HOST', 'galaxy.umassmed.edu');
define('DB_HOST', 'localhost');
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
if (isset($_SESSION['user']))
{
  define('USERNAME', $_SESSION['user']);
  define('UID', $_SESSION['uid']);
}

#define('BASE_PATH','http://biocore.umassmed.edu/dolphin');
#define('BASE_PATH','http://dolphin.umassmed.edu:8080/dolphin');
define('BASE_PATH','http://127.0.0.1/dolphin');
