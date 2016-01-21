<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class ngsquerydb_unittest extends PHPUnit_Framework_TestCase
{
	public function testGetRunSamples(){
		ob_start();
		$_GET['p'] = 'getRunSamples';
		$_GET['gids'] = '1';
		$_GET['run_id'] = '1';
		include("ngsquerydb.php");
		$this->assertEquals(json_decode($data)[0]->sample_id,'1');
		ob_end_clean();
	}
	
	public function testGrabReload(){
		ob_start();
		$_GET['group_id'] = '1';
	}
}

?>