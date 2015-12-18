<?php
//	Include files needed to test ngsimport

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples(){
		$_GET['p'] = 'getTableSamples';
		$_GET['search'] = 1;
		include 'public/ajax/tablegenerator.php';
		var_dump($data);
	}
	
	public function testGetTableRuns(){
		$_GET['p'] = 'getTableRuns';
		$_GET['search'] = 1;
		include 'public/ajax/tablegenerator.php';
		var_dump($data);
	}
	
	//find wkey example
	public function testGetTableReportsList(){
		$_GET['p'] = 'getTableReportsList';
		$_GET['wkey'] = '';
		include 'public/ajax/tablegenerator.php';
		var_dump($data);
	}
	
	public function testSamplesWithRuns(){
		$_GET['p'] = 'sampleWithRuns';
		include 'public/ajax/tablegenerator.php';
		var_dump($data);
	}
}

?>
