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
		$funcs->setCMDs();
		$this->assertEquals($funcs->checkjob_cmd,'ps -ef|grep "[[:space:]][[:space:]]"|awk \'{printf("%s	%s",$8,$2)}\'');
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
		var_dump($params);
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
		ob_end_clean();
	}
	
	public function testGetKey(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals(strlen($funcs->getKey()),30);
		ob_end_clean();
	}
	
	public function testRunSQL(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->runSQL('SELECT * FROM ngs_samples')->id,'1');
		ob_end_clean();
	}
	
	public function testQueryAVal(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->queryAVal('SELECT id FROM ngs_samples WHERE samplename = \'control_rep1\''),'1');
		ob_end_clean();
	}
	
	public function testQueryTable(){
		#ob_start();
		$funcs  = new funcs();
		var_dump($funcs->queryTable('SELECT id FROM ngs_samples WHERE samplename = \'control_rep1\''));
		$this->assertEquals($funcs->queryTable('SELECT id FROM ngs_samples WHERE samplename = \'control_rep1\'')[0]->id,'1');
		#ob_end_clean();
	}
	
	public function testSyscall(){
		ob_start();
		$funcs  = new funcs();
		$command = 'ls funcs.php';
		$this->assertEquals(str_replace("\n", "", $funcs->syscall($command)),'funcs.php');
		$command = 'ls hootnanny';
		$this->assertEquals($funcs->syscall($command),"ERROR 104: Cannot run $command!");
		ob_end_clean();
	}
	
	public function testGetSSH(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->getSSH(),"ssh -o ConnectTimeout=30  ". $funcs->username. "@" . $funcs->remotehost . " ");
		ob_end_clean();
	}
	
	public function testIsJobRunning(){
		ob_start();
		$funcs  = new funcs();
		$this->assertEquals($funcs->isJobRunning('test_wkey', '99999', 'root'), 'EXIT');
		ob_end_clean();
	}
}

?>