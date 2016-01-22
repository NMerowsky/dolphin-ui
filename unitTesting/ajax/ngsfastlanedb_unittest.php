<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class ngsfastlanddb_unittest extends PHPUnit_Framework_TestCase
{
	public function testExperimentSeriesCheck(){
		ob_start();
		$_GET['p'] = 'experimentSeriesCheck';
		$_GET['name'] = '';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
		ob_end_clean();
	}
	
	public function testLaneCheck(){
		ob_start();
		$_GET['p'] = 'laneCheck';
		$_GET['experiment'] = '1';
		$_GET['lane'] = '';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
		ob_end_clean();
	}
	
	public function testSampleCheck(){
		ob_start();
		$_GET['p'] = 'sampleCheck';
		$_GET['experiment'] = '1';
		$_GET['lane'] = '1';
		$_GET['sample'] = '';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
		ob_end_clean();
	}
	
	public function testDirectoryCheck(){
		ob_start();
		$_GET['p'] = 'directoryCheck';
		$_GET['input'] = '';
		$_GET['backup'] = '';
		$_GET['amazon'] = '';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
		ob_end_clean();
	}
	
	public function testInsertExperimentSeries(){
		ob_start();
		$_GET['p'] = 'insertExperimentSeries';
		$_GET['name'] = 'test_series_1';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testInserLane(){
		ob_start();
		$_GET['p'] = 'insertLane';
		$_GET['experiment'] = '3';
		$_GET['name'] = 'test_lane_1';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testInsertSample(){
		ob_start();
		$_GET['p'] = 'insertSample';
		$_GET['experiment'] = '3';
		$_GET['lane'] = '3';
		$_GET['organism'] = 'test_organism';
		$_GET['barcode'] = 'test_barcode';
		$_GET['name'] = 'test_sample_1';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testInsertDirectories(){
		ob_start();
		$_GET['p'] = 'insertDirectories';
		$_GET['input'] = '/input/directory/test';
		$_GET['backup'] = '/backup/directory/test';
		$_GET['amazon'] = 'S3:testAmazon';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testInsertTempSample(){
		ob_start();
		$_GET['p'] = 'insertTempSample';
		$_GET['filename'] = 'test_filename_sample';
		$_GET['sample_id'] = '11';
		$_GET['input'] = '3';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testInsertTempLane(){
		ob_start();
		$_GET['p'] = 'insertTempLane';
		$_GET['file_name'] = 'test_filename_lane';
		$_GET['lane_id'] = '3';
		$_GET['dir_id'] = '3';
		$_GET['gids'] = '1';
		$_GET['perms'] = '32';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
	}
	
	public function testSendProcessData(){
		ob_start();
		$_GET['p'] = 'sendProcessData';
		$_GET['info_array'] = array('test1','test2');
		$_GET['post'] = 'test_post';
		include("ngsfastlanedb.php");
		$this->assertEquals($_SESSION['test_post'],'test1,test2');
		ob_end_clean();
	}
	
	public function testObtainGroupFromName(){
		ob_start();
		$_GET['p'] = 'obtainGroupFromName';
		$_GET['name'] = 'umw_biocore';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->id,'1');
		ob_end_clean();
	}
	
	public function testGetClusterName(){
		ob_start();
		$_GET['p'] = 'getClusterName';
		include("ngsfastlanedb.php");
		$this->assertEquals(json_decode($data)[0]->username,'kucukura');
		ob_end_clean();
	}
}

?>