<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class statquery_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetDailyRuns(){
		echo '@@@@@@@@@@@@@@@@@@@@@@';
		$_GET['p'] = 'getDailyRuns';
		include('statquery.php');
		$this->assertEquals(1,1);
	}
}

?>