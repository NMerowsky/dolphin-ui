<?php
//	Include files needed to test ngsimport
include 'config/config.php';
include 'config/inflection.php';
include 'library/inflection.class.php';
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';

$_SESSION['uid'] = 1;
$_SESSION['gids'] = 1;
$_SESSION['user'] = 'kucukura';

include 'application/models/ngsimport.php';

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public $worksheetPathSet = 'false';
	
	/*
	 *	function:		testNum2Alpha
	 *	description:	tests the num2alpha function for accuracy
	 *					for excel spreadsheet column pinpointing
	 */
	public function testNum2Aplha() {
		$ngsimport = new Ngsimport();
		$this->assertEquals($ngsimport->num2alpha(0),'A');
		$this->assertEquals($ngsimport->num2alpha(25),'Z');
		$this->assertEquals($ngsimport->num2alpha(26),'AA');
		$this->assertEquals($ngsimport->num2alpha(51),'AZ');
  	}
	
	/*
	 *	function:		testColumnNumber
	 *	description:	tests the columnNumber function for accuracy
	 *					for excel spreadsheet column pinpointing
	 */
	public function testColumnNumber() {
		$ngsimport = new Ngsimport();
		$this->assertEquals($ngsimport->columnNumber('A'),1);
		$this->assertEquals($ngsimport->columnNumber('Z'),26);
		$this->assertEquals($ngsimport->columnNumber('AA'),27);
		$this->assertEquals($ngsimport->columnNumber('AZ'),52);
	}
	
	/*
	 *	function:		testGetGroup
	 *	description:	tests the getGroup function for accuracy
	 *					in obtaining a list of groups in which the user is a part of
	 */
	public function testGetGroup() {
		$ngsimport = new Ngsimport();
		echo $ngsimport->getGroup('kucukura');
		$this->assertContains($ngsimport->getGroup('kucukura'),'1');
	}
	
	/*
	 *	function:		testCreateSampleName
	 *	description:	tests the createSampleName function for accuracy
	 *					in creating the defined 'samplename' layout for the DC project samples
	 */
	public function testCreateSampleName() {
		$ngsimport = new Ngsimport();
		
		//	Sample needed for function
		$samp = new sample();
		$samp->name = 'test_sample';
		$samp->donor = 'D01';
		$samp->source_symbol = 'MDDC';
		$samp->condition_symbol = 'LPS';
		$samp->time = 60;
		
		$nobar = new sample();
		$nobar->name = 'nobarcode';
		
		$rand = new sample();
		$rand->name = 'random_sample_name';
		
		//	Check for example template files for testing.
		$this->assertFileExists('public/downloads/example_template_multi_dirs.xls');
		$this->assertFileExists('public/downloads/example_template.xls');
		
		$this->assertEquals($ngsimport->createSampleName($samp),'D01_MDDC_Lps_1h');
		$this->assertEquals($ngsimport->createSampleName($nobar),'nobarcode');
		$this->assertEquals($ngsimport->createSampleName($rand),'');
	}
	
	/*
	 *	function:		testParseExcel
	 *	description:	tests the parseExcel function for accuracy
	 */
	public function testParseExcel(){
		//	Function requires gid, uid, worksheet, sheet data, and passed_final_check
		$gid = 1;
		$uid = 1;
		$inputFileType = 'Excel5';
		$inputFileName = 'public/downloads/example_template_multi_dirs.xls';
		$worksheetData = $this->worksheetTestGenerator();
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);
		$passed_final_check = true;
		
		$ngsimport = new Ngsimport();
		foreach ($worksheetData as $worksheet) {
			$objPHPExcel->setActiveSheetIndexByName($worksheet['worksheetName']);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$parseArray = $ngsimport->parseExcel($gid, $uid, $worksheet, $sheetData, $passed_final_check);
			$passed_final_check = $parseArray[0];
			$this->assertEquals($parseArray[0], '1');
		}
		return $passed_final_check;
	}
	
	/*
	*	function:		testFinalizeExcel
	*	description:	tests the parseExcel function for accuracy
	*	@depends testParseExcel
	*/
	public function testFinalizeExcel(){
		$passed_final_check = func_get_args();
		$worksheetData = $this->worksheetTestGenerator();
		echo implode(', ',$passed_final_check);
		var_dump($passed_final_check);
		
	}
	
	/*
	 *	function:		testTextTypes
	 *	description:	tests the text outputs for correct color scheme
	 */
	public function testTextTypes(){
		$ngsimport = new Ngsimport();
		$this->assertContains($ngsimport->errorText('kucukura'),'red');
		$this->assertContains($ngsimport->warningText('kucukura'),'B45F04');
		$this->assertContains($ngsimport->successText('kucukura'),'green');
	}
	
	/*
	*	function:		worksheetTestGenerator
	*	description:	Grabs worksheet information for test cases
	*/
	public function worksheetTestGenerator(){
		//	Include necessary excel classes if not already loaded
		if($this->worksheetPathSet == 'false'){
			set_include_path('includes/excel/Classes/');
			include 'PHPExcel/IOFactory.php';
			$this->worksheetPathSet = 'true';
		}
		
		$inputFileType = 'Excel5';
		$inputFileName = 'public/downloads/example_template_multi_dirs.xls';
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$worksheetData = $objReader->listWorksheetInfo($inputFileName);
		
		return $worksheetData;
	}
}

?>
