<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
chdir('public/ajax/');

echo 'test';

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples() {
		include 'tablegenerator.php';
		echo $_GET['p'];
		echo $data;
		$this->assertEquals($_GET['search'],1);
	}
	
	public function testGetTableRuns() {
		$_GET['p'] = 'getTableRuns';
		$_GET['search'] = 1;
		$data = include 'tablegenerator.php';
		echo $_GET['p'];
		echo $data;
	}
	
	//find wkey example
	public function testGetTableReportsList() {
		$_GET['p'] = 'getTableReportsList';
		$_GET['wkey'] = '';
		$data = include 'tablegenerator.php';
		echo $_GET['p'];
		echo $data;
	}
	
	public function testSamplesWithRuns() {
		$_GET['p'] = 'sampleWithRuns';
		$data = include 'tablegenerator.php';
		echo $_GET['p'];
		echo $data;
	}
}

?>
