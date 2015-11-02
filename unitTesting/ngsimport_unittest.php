<?php
//	Include files needed to test ngsimport
include 'config/config.php';
include 'config/inflection.php';
include 'library/inflection.class.php';
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';

/*
	$_SESSION['uid'] = 1;
	$_SESSION['gids'] = 1;
	$_SESSION['user'] = 'kucukura';
*/

include 'application/models/ngsimport.php';

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
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
		$samp->donor = 'D01';
		$samp->source_symbol = 'MDDC';
		$samp->condition_symbol = 'LPS';
		$samp->time = 60;
		
		$nobar = new sample();
		$nobar->name = 'nobarcode';
		
		//	Check for example template files for testing.
		$this->assertFileExists('public/downloads/example_template_multi_dirs.xls');
		$this->assertFileExists('public/downloads/example_template.xls');
		
		$this->assertEquals($ngsimport->createSampleName($samp),'D01_MDDC_Lps_1h');
		$this->assertEquals($ngsimport->createSampleName($nobar),'nobarcode');
	}
}

?>
