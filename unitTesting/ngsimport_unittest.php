<?php

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
	public function testNum2Aplha() {
		$ngsimport = new Ngsimport();
		$this->assertEquals($ngsimport->num2alpha(0),'A');
		$this->assertEquals($ngsimport->num2alpha(25),'Z');
		$this->assertEquals($ngsimport->num2alpha(26),'AA');
		$this->assertEquals($ngsimport->num2alpha(51),'AZ');
  	}
	
	public function testColumnNUmber() {
		$ngsimport = new Ngsimport();
		$this->assertEquals($ngsimport->columnNumber('A'),1);
		$this->assertEquals($ngsimport->columnNumber('Z'),26);
		$this->assertEquals($ngsimport->columnNumber('AA'),27);
		$this->assertEquals($ngsimport->columnNumber('AZ'),52);
	}
}

?>
