<?php
//	Include files needed to test ngsimport
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['uid'] = '1';
$_SESSION['user'] = 'kucukura';
chdir('public/ajax/');

class kill_pid_unittest extends PHPUnit_Framework_TestCase
{
    public function testKillRun(){
        $_GET['p'] = 'killRun';
        $_GET['run_id'] = '1';
        include("kill_pid.php");
        $this->assertEquals($data,'');
    }
}

?>