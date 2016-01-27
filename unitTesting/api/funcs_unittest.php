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
		$this->assertEquals($funcs->schedular,'N');
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
		$this->assertEquals($funcs->schedular,'N');
		ob_end_clean();
	}
	
	public function testSetCMDs(){
		ob_start();
		$funcs  = new funcs();
		$funcs = $funcs->setCMDs();
		$this->assertEquals($funcs->checkjob_cmd,'');
		ob_end_clean();
	}
	
	public function testGetCMDs(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->getCMDS(''),'');
		ob_end_clean();
	}
	
	public function testCheckFile(){
		ob_start();
		$funcs  = new funcs();
		$params['username'] = 'root';
		$params['file'] = 'funcs.php';
		$this->assertEquals($funcs->checkFile($params),"{\"Result\":\"Ok\"}");
		$params['username'] = 'root';
		$params['file'] = 'does_not_exist.php';
		$this->assertEquals($funcs->checkFile($params),"{\"ERROR\": \"No such file or directory: ".$params['file']."\"}");
		ob_end_clean();
	}
	
	public function testCheckPermissions(){
		ob_start();
		$funcs  = new funcs();
		$params['username'] = 'root';
		$params['outdir'] = 'funcs_dir_test';
		$this->assertEquals($funcs->checkPermissions($params),"{\"Result\":\"Ok\"}");
		$params['username'] = 'random';
		$params['outdir'] = 'funcs_dir_test';
		$this->assertEquals($funcs->checkPermissions($params),"{\"ERROR\": \"Permission denied: ".$params['outdir']."\"}");
		ob_end_clean();
	}
	
	public function testGetKey(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->getKey(),'');
		ob_end_clean();
	}
}

?>