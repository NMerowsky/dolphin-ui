<?php
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';
include 'application/models/ngsimport.php';

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testOnePlusOne() {
		$this->assertEquals(1+1,2);
  	}
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
?>
