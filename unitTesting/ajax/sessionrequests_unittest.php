<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class sessionrequests_unittest extends PHPUnit_Framework_TestCase
{
	public function testSessionTest(){
		$_GET['p'] = 'sessionTest';
		include('sessionrequests.php');
		$this->assertEquals(1,1);
	}
	
	public function testSendBasketInfo(){
		$_GET['p'] = 'sendBasketInfo';
		$_POST['id'] = '2';
		$_SESSION['basket'] = '1';
		include('sessionrequests.php');
		echo $_SESSION['basket'];
		$this->assertEquals($_SESSION['basket'],'1,2');
	}
}

?>