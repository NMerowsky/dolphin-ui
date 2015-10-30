<?php

require_once 'config/config.php';
require_once 'library/sqlquery.class.php';
require_once 'library/vanillamodel.class.php';
require_once 'library/vanillacontroller.class.php';

$_SESSION['uid'] = 1;
$_SESSION['gids'] = 1;
$_SESSION['user'] = 'kucukura';

require_once 'application/controllers/ngsimportcontroller.php';
$ngsimportcontroller = new ngsimportcontroller();

require_once 'application/models/ngsimport.php';
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
