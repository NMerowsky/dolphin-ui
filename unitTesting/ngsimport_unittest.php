<?php

include 'config/config.php';
include 'library/inflection.class.php';
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';

$_SESSION['uid'] = 1;
$_SESSION['gids'] = 1;
$_SESSION['user'] = 'kucukura';

require_once 'application/models/ngsimport.php';
$ngs = new Ngsimport();

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{	
	public function testNum2Aplha() {
		echo $ngs;
		$ngsimport = new Ngsimport();
		echo $ngsimport;
		$this->assertEquals($ngsimport->num2alpha(3),'C');
  	}
}

?>
