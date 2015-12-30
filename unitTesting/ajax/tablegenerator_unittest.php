<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
chdir('public/ajax/');

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples() {
		$p = 'getTableSamples';
		$search = 7;
		include('tablegenerator.php');
		$this->assertEquals(json_encode($data)->samplename,'example_sample_1');
	}
	
	public function testGetTableRuns() {
		$this->assertEquals(1,1);
		$_GET['p'] = 'getTableRuns';
		$_GET['search'] = 1;
		//include 'tablegenerator.php';
		$this->assertEquals(1,1);
	}
	
	//find wkey example
	public function testGetTableReportsList() {
		$this->assertEquals(1,1);
		$_GET['p'] = 'getTableReportsList';
		$_GET['wkey'] = '';
		//include 'tablegenerator.php';
		$this->assertEquals(1,1);
	}
	
	public function testSamplesWithRuns() {
		$this->assertEquals(1,1);
		$_GET['p'] = 'sampleWithRuns';
		//include 'tablegenerator.php';
		$this->assertEquals(1,1);
	}
}

?>
