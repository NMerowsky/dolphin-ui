<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class ngs_tables_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetStatus(){
		ob_start();
		$_POST['p'] = 'getStatus';
		$_POST['q'] = '';
		$_POST['r'] = '';
		$_POST['seg'] = '';
		$_POST['search'] = '';
		$_POST['uid'] = '1';
		$_POST['gids'] = '1';
		include("ngs_tables.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
	}
}

?>