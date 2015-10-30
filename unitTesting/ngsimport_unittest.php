<?php

include 'config/config.php';
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';

include 'application/models/ngsimport.php';


#include 'includes/dbfuncs.php';
#$query = new dbfuncs();

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testNum2Aplha() {
		echo 'testNum2Alpha';
		$ngsimport = new Ngsimport();
		$this->assertEquals($ngsimport->num2alpha(3),'D');
  	}
}

?>
