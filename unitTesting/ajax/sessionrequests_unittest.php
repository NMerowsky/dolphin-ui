<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = 1;
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class sessionrequests_unittest extends PHPUnit_Framework_TestCase
{
	public function testSessionTest(){
		$_GET['p'] = 'sessionTest';
		include('sessionrequests.php');
		$this->assertEquals(1,1);
	}
	
	public function testSendBasketInfo(){
		$_GET['p'] = 'sendBasketInfo';
		$_POST['id'] = '2';
		$_SESSION['basket'] = '1';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['basket'],'1,2');
	}
	
	public function testGetBasketInfo(){
		$_GET['p'] = 'getBasketInfo';
		$_SESSION['basket'] = '1';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['basket'],'1');
	}
	
	public function testRemoveBasketInfo(){
		$_GET['p'] = 'removeBasketInfo';
		$_POST['id'] = '2';
		$_SESSION['basket'] = '1,2';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['basket'],'1');
	}
	
	public function testFlushBasketInfo(){
		$_GET['p'] = 'flushBasketInfo';
		$_SESSION['basket'] = '1,2';
		include('sessionrequests.php');
		$this->assertEquals(isset($_SESSION['basket']),false);
	}
	
	public function testSendWKey(){
		$_GET['p'] = 'sendWKey';
		$_POST['wkey'] = 'wkey';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['wkey'],'wkey');
	}
	
	public function testGetWkey(){
		$_GET['p'] = 'getWKey';
		$_POST['wkey'] = 'wkey';
		include('sessionrequests.php');
		$this->assertEquals($_POST['wkey'],'wkey');
	}
	
	public function testFlushWKey(){
		$_GET['p'] = 'flushWKey';
		$_SESSION['wkey'] = 'wkey';
		include('sessionrequests.php');
		$this->assertEquals(isset($_SESSION['wkey']),false);
	}
	
	public function testTableToggle(){
		$_GET['p'] = 'tableToggle';
		$_GET['table'] = 'samples';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['ngs_samples'],'extend');
	}
	
	public function testGetTableToggle(){
		$_GET['p'] = 'getTableToggle';
		$_GET['table'] = 'samples';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['ngs_samples'],'');
	}
	
	public function testSetPlotToggle(){
		$_GET['p'] = 'setPlotToggle';
		$_GET['type'] = 'generated';
		$_GET['file'] = 'test.file';
		include('sessionrequests.php');
		$this->assertEquals($_SESSION['plot_file'],'test.file');
	}
	
	public function testGetPlotToggle(){
		$_GET['p'] = 'getPlotToggle';
		$_SESSION['plot_type'] = 'generated';
		$_SESSION['plot_file'] = 'test.file';
		include('sessionrequests.php');
		$this->assertEquals(isset($_SESSION['plot_file']),true);
	}
	
	public function testChangeRunType(){
		$_GET['p'] = 'changeRunType';
		$_GET['run_type'] = '1';
		include('sessionrequests.php');
		$this->assertEquals(isset($_SESSION['run_type']),true);
		$this->assertEquals($_SESSION['run_type'],1);
	}
	
	public function testGetRunType(){
		$_GET['p'] = 'getRunToggle';
		include('sessionrequests.php');
		$this->assertEquals(isset($_SESSION['run_type']),false);
	}
}

?>