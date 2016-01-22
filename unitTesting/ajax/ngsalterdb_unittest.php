<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class ngsalterdb_unittest extends PHPUnit_Framework_TestCase
{
	
	public function testKillPid(){
		ob_start();
		$_POST['p'] = 'none';
		include("ngsalterdb.php");
		$wkey = killPid('1',$query);
		$this->assertEquals($wkey,'J98Oe0bSZ18fBx9pPuDnsD8ITRVPGV');
		ob_end_clean();
	}
	
	public function testSubmitPipelineInsert(){
		#ob_start();
		$_POST['p'] = 'submitPipeline';
		$_POST['json'] = 'test_json';
		$_POST['outdir'] = '/test/outdir';
		$_POST['name'] = 'test insertPipeline';
		$_POST['desc'] = 'unittesting insertPipeline';
		$_POST['runGroupID'] = 'new';
		$_POST['barcode'] = 'none';
		$_POST['uid'] = '1';
		$_POST['group'] = '1';
		$_POST['perms'] = '32';
		include("ngsalterdb.php");
		var_dump($data);
		$this->assertEquals(json_decode($data),'4');
		#ob_end_clean();
	}
	
	public function testSubmitPipelineUpdate(){
		ob_start();
		$_POST['p'] = 'submitPipeline';
		$_POST['json'] = 'test_json_update';
		$_POST['outdir'] = '/test/outdir';
		$_POST['name'] = 'test insertPipeline update';
		$_POST['desc'] = 'unittesting insertPipeline update';
		$_POST['runGroupID'] = '4';
		$_POST['barcode'] = 'none';
		$_POST['uid'] = '1';
		$_POST['group'] = '1';
		$_POST['perms'] = '32';
		include("ngsalterdb.php");
		$this->assertEquals(json_decode($data),'4');
		ob_end_clean();
	}
	
	public function testInsertRunList(){
		#ob_start();
		$_POST['p'] = 'insertRunList';
		$_POST['sampID'] = '1';
		$_POST['runID'] = '4';
		$_POST['uid'] = '1';
		$_POST['gids'] = '1';
		include("ngsalterdb.php");
		var_dump($searchQuery);
		var_dump($data);
		$this->assertEquals(json_decode($data),'1');
		#ob_end_clean();
	}
	
	public function testNoAddedParamsRerun(){
		ob_start();
		$_POST['p'] = 'noAddedParamsRerun';
		$_POST['run_id'] = '4';
		include("ngsalterdb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testDeleteRunparams(){
		ob_start();
		$_POST['p'] = 'deleteRunparams';
		$_POST['run_id'] = '3';
		include("ngsalterdb.php");
		$this->assertEquals($run_id,'3');
		ob_end_clean();
	}
	
	public function testUpdateProfile(){
		ob_start();
		$_POST['p'] = 'updateProfile';
		$_POST['img'] = 'test_img.png';
		include("ngsalterdb.php");
		$this->assertEquals(json_decode($data),'1');
		ob_end_clean();
	}
	
	public function testAlterAccessKey(){
		ob_start();
		$_POST['p'] = 'alterAccessKey';
		$_POST['id'] = '1';
		$_POST['a_key'] = 'ngsalterdb new key';
		include("ngsalterdb.php");
		$this->assertEquals(json_decode($data),'1');
		ob_end_clean();
	}
	
	public function testAlterSecretKey(){
		ob_start();
		$_POST['p'] = 'alterSecretKey';
		$_POST['id'] = '1';
		$_POST['s_key'] = 'ngsalterdb new secret key';
		include("ngsalterdb.php");
		$this->assertEquals(json_decode($data),'1');
		ob_end_clean();
	}
}

?>