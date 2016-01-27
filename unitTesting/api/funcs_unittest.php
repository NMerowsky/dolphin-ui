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
		$this->assertEquals($funcs->dbhost,'localhost');
		$this->assertEquals($funcs->db,'biocore');
		$this->assertEquals($funcs->dbuser,'bioinfo');
		$this->assertEquals($funcs->dbpass,'bioinfo2013');
		$this->assertEquals($funcs->tool_path,'/usr/local/share/dolphin_tools/src');
		$this->assertEquals($funcs->remotehost,'N');
		$this->assertEquals($funcs->jobstatus,'N');
		$this->assertEquals($funcs->config,'Docker');
		$this->assertEquals($funcs->python,'python');
		$this->assertEquals($funcs->scheduler,'N');
		ob_end_clean();
	}
	
	public function testGetINI(){
		ob_start();
		$funcs  = new funcs();
		$funcs = $funcs->getINI();
		$this->assertEquals($funcs->dbhost,'localhost');
		$this->assertEquals($funcs->db,'biocore');
		$this->assertEquals($funcs->dbuser,'bioinfo');
		$this->assertEquals($funcs->dbpass,'bioinfo2013');
		$this->assertEquals($funcs->tool_path,'/usr/local/share/dolphin_tools/src');
		$this->assertEquals($funcs->remotehost,'N');
		$this->assertEquals($funcs->jobstatus,'N');
		$this->assertEquals($funcs->config,'Docker');
		$this->assertEquals($funcs->python,'python');
		$this->assertEquals($funcs->scheduler,'N');
		ob_end_clean();
	}
}

?>