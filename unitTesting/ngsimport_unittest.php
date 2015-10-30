<?php

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	include("../config/config.php");
	include("../includes/dbfuncs.php");

	public function testOnePlusOne() {
		$this->assertEquals(1+1,2);
  	}
}

?>
