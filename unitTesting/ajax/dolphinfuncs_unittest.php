<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class dolphinfuncs_unittest extends PHPUnit_Framework_TestCase
{
    public function testGetFileList(){
        ob_start();
		$_GET['p'] = 'getFileList';
		include("dolphinfuncs.php");
		$this->assertEquals(json_decode($data)[0]->file_name,'');
		ob_end_clean();
    }
    
    public function testUpdateHashBackup(){
        ob_start();
		$_GET['p'] = 'updateHashBackup';
        $_GET['input'] = '';
        $_GET['dirname'] = '';
        $_GET['hashstr'] = 'test_hash';
		include("dolphinfuncs.php");
		$this->assertEquals(json_decode($data),'0');
		ob_end_clean();
    }
}

?>