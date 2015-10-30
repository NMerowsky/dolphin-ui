<?php

class ngsimport_unittest extends PHPUnit_Framework_TestCase
{
	public function testOnePlusOne() {
	    include 'application/models/ngsimport.php';
		include 'config/config.php';
		
		$this->assertEquals(1+1,2);
  	}
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
?>
