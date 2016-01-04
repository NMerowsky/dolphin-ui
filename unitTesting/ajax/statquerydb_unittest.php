<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class statquerydb_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetDailyRuns(){
		$_GET['p'] = 'getDailyRuns';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetTopUsers(){
		$_GET['p'] = 'getTopUsers';
		$_GET['type'] = 'Dolphin';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetTopUsersTime(){
		$_GET['p'] = 'getTopUsersTime';
		$_GET['type'] = 'Dolphin';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetUsersTime(){
		$_GET['p'] = 'getUsersTime';
		$_GET['type'] = 'Dolphin';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetLabsTime(){
		$_GET['p'] = 'getLabsTime';
		$_GET['type'] = 'Dolphin';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetToolTime(){
		$_GET['p'] = 'getToolTime';
		$_GET['type'] = 'Dolphin';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
	
	public function testGetJobTime(){
		$_GET['p'] = 'getJobTime';
		include('statquerydb.php');
		$this->assertEquals(1,1);
	}
}

?>