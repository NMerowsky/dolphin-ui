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
		$this->assertEquals(json_decode($data)[0]->samplename,'example_sample_1');
	}
	
	public function testGetTableRuns() {
		$_GET['p'] = 'getTableRuns';
		$_GET['search'] = 1;
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->sample_id,1);
	}
	
	//find wkey example
	public function testGetTableReportsList() {
		$_GET['p'] = 'getTableReportsList';
		$_GET['wkey'] = 'J98Oe0bSZ18fBx9pPuDnsD8ITRVPGV';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->file,'');
	}
	
	public function testSamplesWithRuns() {
		$_GET['p'] = 'sampleWithRuns';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->sample_id,1);
	}
}

?>
