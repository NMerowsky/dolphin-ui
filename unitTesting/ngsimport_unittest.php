<?php

include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';
include 'application/models/ngsimport.php';
include 'config/config.php';
include 'includes/dbfuncs.php';

$query = new dbfuncs();
$ngsimport = new Ngsimport();


class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testOnePlusOne() {
		$this->assertEquals(1+1,2);
  	}
}

?>
