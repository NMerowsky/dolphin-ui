<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
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
	
	public function testCreateTableFile(){
		$p = 'createTableFile';
		$url = '/home/travis/build/Rhaknam/dolphin-ui/public/api/getsamplevals.php';
		$_GET['samples'] = 'samples=1,2,3,4,5,6:3';
		$_GET['file'] = 'file=rsem/genes_expression_tpm.tsv';
		$_GET['common'] = 'common=gene,transcript';
		$_GET['key'] = 'key=gene';
		$_GET['format'] = 'format=json2';
		include('tablegenerator.php');
		$file = json_decode($data);
		$this->assertEquals(json_decode($data),$file);
		return $file;
	}
	
	/**
	 * @depends testCreateTableFile
	 */
	public function testCreateNewTable($file){
		$p = 'createNewTable';
		$search = 'samples=1,2,3,4,5,6:3&file=rsem/genes_expression_tpm.tsv&common=gene,transcript&key=gene&format=json';
		$name = 'test_table';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'true');
	}
	
	public function testConvertToTSV(){
		$p = 'convertToTSV';
		$url = '/home/travis/build/Rhaknam/dolphin-ui/public/api/getsamplevals.php?samples=1,2,3,4,5,6:3&file=rsem/genes_expression_tpm.tsv&common=gene,transcript&key=gene&format=json';
		include('tablegenerator.php');
		$file = json_decode($data);
		$this->assertEquals(json_decode($data),$file);
		return $file;
	}
	
	public function testGetCreatedTables(){
		$p = 'getCreatedTables';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->name,'test_table');
	}
	
	public function testDeleteTable(){
		$p = 'deleteTable';
		$id = '1';
		$_GET['id'] = '1';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'1');
	}
	
	/**
	 * @depends testConvertToTSV
	 */
	public function testRemoveTSV($file){
		$p = 'removeTSV';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'deleted');
	}
}

?>
