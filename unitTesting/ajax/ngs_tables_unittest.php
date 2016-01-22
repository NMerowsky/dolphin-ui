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
		$_GET['p'] = 'getStatus';
		$_GET['q'] = '';
		$_GET['r'] = '';
		$_GET['seg'] = '';
		$_GET['search'] = '';
		$_GET['uid'] = '1';
		$_GET['gids'] = '1';
		include("ngs_tables.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
	}
	
	public function testGetSelectedSamples(){
		ob_start();
		$_GET['p'] = 'getSelectedSamples';
		$_GET['q'] = '';
		$_GET['r'] = '';
		$_GET['seg'] = '';
		$_GET['search'] = '';
		$_GET['uid'] = '1';
		$_GET['gids'] = '1';
		include("ngs_tables.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
	}
}

?>