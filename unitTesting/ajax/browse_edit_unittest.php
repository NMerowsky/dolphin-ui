<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class browse_edit_unittest extends PHPUnit_Framework_TestCase
{
    public function testUpdateDatabase(){
        ob_start();
		$_GET['p'] = 'updateDatabase';
        $_GET['type'] = 'lanes';
        $_GET['table'] = 'ngs_lanes';
        $_GET['value'] = '1';
		include("browse_edit.php");
		$this->assertEquals(json_decode($data),'1');
		ob_end_clean();
		
    }
}

?>