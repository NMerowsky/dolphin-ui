<?php
//	Include files needed to test ngsimport
include 'config/config.php';

class tablegenerator_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetTableSamples(){
		$_GET['p'] = 'getTableSamples';
		$_GET['search'] = 1;
		include 'public/ajax/tablegenerator.php';
		var_dump($data);
	}
	
}

?>
