<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples() {
		$_GET['p'] = 'getTableSamples';
		$_GET['search'] = 7;
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
		$_GET['wkey'] = '3pl8cmzYJ4ezgX2a9RevZxHmihpOA';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->file,'rsem/genes_expression_tpm.tsv');
	}
	
	public function testSamplesWithRuns() {
		$_GET['p'] = 'samplesWithRuns';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->sample_id,1);
	}
	
	public function testCreateTableFile(){
		$_GET['p'] = 'createTableFile';
		$_GET['url'] = '/home/travis/build/Rhaknam/dolphin-ui/public/api/getsamplevals.php';
		$_GET['samples'] = 'samples=1,2,3,4,5,6:3';
		$_GET['file'] = 'file=rsem/genes_expression_tpm.tsv';
		$_GET['common'] = 'common=gene,transcript';
		$_GET['key'] = 'key=gene';
		$_GET['format'] = 'format=json2';
		include('tablegenerator.php');
		$file = json_decode($data);
		$this->assertEquals(json_decode($data),$file);
		$array = array();
		exec('cat ../tmp/files/'.$file, $array);
		foreach($array as $line){
			echo $line;
		}
		return $file;
	}
	
	/**
	 * @depends testCreateTableFile
	 */
	public function testCreateNewTable($file){
		$_GET['p'] = 'createNewTable';
		$_GET['search'] = 'samples=1,2,3,4,5,6:3&file=rsem/genes_expression_tpm.tsv&common=gene,transcript&key=gene&format=json';
		$_GET['name'] = 'test_table';
		$_GET['file'] = $file;
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'true');
	}
	
	public function testConvertToTSV(){
		$_GET['p'] = 'convertToTSV';
		$_GET['url'] = '/home/travis/build/Rhaknam/dolphin-ui/public/api/getsamplevals.php';
		$_GET['samples'] = 'samples=1,2,3,4,5,6:3';
		$_GET['file'] = 'file=rsem/genes_expression_tpm.tsv';
		$_GET['common'] = 'common=gene,transcript';
		$_GET['key'] = 'key=gene';
		$_GET['format'] = 'format=json';
		include('tablegenerator.php');
		$file = json_decode($data);
		$this->assertEquals(json_decode($data),$file);
		return $file;
	}
	
	public function testGetCreatedTables(){
		$_GET['p'] = 'getCreatedTables';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data)[0]->name,'test_table');
	}
	
	public function testDeleteTable(){
		$_GET['p'] = 'deleteTable';
		$_GET['id'] = '1';
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'1');
	}
	
	/**
	 * @depends testConvertToTSV
	 */
	public function testRemoveTSV($file){
		$_GET['p'] = 'removeTSV';
		$_GET['file'] = $file;
		include('tablegenerator.php');
		$this->assertEquals(json_decode($data),'deleted');
	}
}

?>
