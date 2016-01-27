<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class adminstatquerydb_unittest extends PHPUnit_Framework_TestCase
{
    public function testGetDailyRuns(){
        ob_start();
		$_GET['p'] = 'getDailyRuns';
		include("adminstatquerydb.php");
		$this->assertEquals(json_decode($data),array());
		ob_end_clean();
    }
    
    public function testGetTopUsers(){
        ob_start();
		$_GET['p'] = 'getTopUsers';
        $_GET['type'] = 'Dolphin';
		include("adminstatquerydb.php");
		$this->assertEquals(json_decode($data),array());
		ob_end_clean();
    }
}

?>