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
		$this->assertEquals($ngsimport->num2alpha(3),'C');
  	}
}

?>
