<?php

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	require_once("../config/config.php");
	require_once("../includes/dbfuncs.php");

	public function testOnePlusOne() {
		$this->assertEquals(1+1,2);
  	}
}

?>
