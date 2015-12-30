<?php
//	Include files needed to test ngsimport

chdir('public/ajax/');

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples(){
		$_GET['p'] = 'getTableSamples';
		$_GET['search'] = 1;
		$data = include 'tablegenerator.php';
		var_dump($data);
		$this->assertEquals($_GET['search'],1);
	}
	
	public function testGetTableRuns(){
		$_GET['p'] = 'getTableRuns';
		$_GET['search'] = 1;
		$data = include 'tablegenerator.php';
		var_dump($data);
	}
	
	//find wkey example
	public function testGetTableReportsList(){
		$_GET['p'] = 'getTableReportsList';
		$_GET['wkey'] = '';
		$data = include 'tablegenerator.php';
		var_dump($data);
	}
	
	public function testSamplesWithRuns(){
		$_GET['p'] = 'sampleWithRuns';
		$data = include 'tablegenerator.php';
		var_dump($data);
	}
}

?>
