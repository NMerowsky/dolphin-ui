<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
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
		$p = 'getTableRuns';
		$search = 1;
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->sample_id,1);
	}
	
	//find wkey example
	public function testGetTableReportsList() {
		$p = 'getTableReportsList';
		$wkey = '3pl8cmzYJ4ezgX2a9RevZxHmihpOA';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->file,'rsem/genes_expression_tpm.tsv');
	}
	
	public function testSamplesWithRuns() {
		$p = 'samplesWithRuns';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->sample_id,1);
	}
	
	public function testCreateNewTable(){
		$p = 'createNewTable';
		$search = 'samples=1,2,3,4,5,6:3&file=rsem/genes_expression_tpm.tsv&common=gene,transcript&key=gene&format=json';
		$file = 'kucukura_2015-11-05-15-07-05.json2';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'true');
	}
	
	public function testGetCreatedTables(){
		
	}
	
	public function testDeleteTable(){
		
	}
}

?>
