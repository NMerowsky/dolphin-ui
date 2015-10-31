<?php

include 'config/config.php';
include 'library/sqlquery.class.php';
include 'library/vanillamodel.class.php';
include 'library/vanillacontroller.class.php';
include 'library/inflection.class.php';

$_SESSION['uid'] = 1;
$_SESSION['gids'] = 1;
$_SESSION['user'] = 'kucukura';

#include 'application/controllers/ngsimportcontroller.php';
#$ngsimportcontroller = new ngsimportcontroller();

include 'application/models/ngsimport.php';
$ngsimport = new ngsimport();

#include 'includes/dbfuncs.php';
#$query = new dbfuncs();

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testNum2Aplha() {
		echo 'testNum2Alpha';
		$this->assertEquals($ngsimport->num2alpha(3),'D');
  	}
}

?>
