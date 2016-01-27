<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/api/');

include 'funcs.php';

class funcs_unittest extends PHPUnit_Framework_TestCase
{
	public function testReadINI(){
		ob_start();
		$funcs  = new funcs();
		$funcs->readINI();
		$this->assertEquals($funcs->dbhost,'');
		$this->assertEquals($funcs->db,'');
		$this->assertEquals($funcs->dbuser,'');
		$this->assertEquals($funcs->dbpass,'');
		$this->assertEquals($funcs->tool_path,'');
		$this->assertEquals($funcs->remotehost,'');
		$this->assertEquals($funcs->jobstatus,'');
		$this->assertEquals($funcs->config,'');
		$this->assertEquals($funcs->python,'');
		$this->assertEquals($funcs->scheduler,'');
		ob_end_clean();
	}
}

?>