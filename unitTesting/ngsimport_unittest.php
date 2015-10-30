<?php

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testOnePlusOne() {
		include 'config/config.php';
	        include 'includes/dbfuncs.php';

		$this->assertEquals(1+1,2);
  	}
}

?>
