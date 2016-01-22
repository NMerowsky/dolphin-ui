<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class ngsalterdb_unittest extends PHPUnit_Framework_TestCase
{
	public function testRunCmd(){
		ob_start();
		include("ngsfastlanedb.php");
		runCmd('3', $query, '');
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testKillPid(){
		ob_start();
		include("ngsfastlanedb.php");
		$wkey = killPid('1',$query);
		$this->assertEquals($wkey,'J98Oe0bSZ18fBx9pPuDnsD8ITRVPGV');
		ob_end_clean();
	}
}

?>