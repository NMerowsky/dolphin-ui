<?php
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../../config/config.php");
require_once("../../includes/dbfuncs.php");

if (isset($_GET['p'])){$p = $_GET['p'];}
if (isset($_POST['p'])){$p = $_POST['p'];}

if($p == "sessionTest")
{
	echo $_SESSION['uid'];
}
else if ($p == "sendBasketInfo")
{
	if (isset($_POST['id'])){$id = $_POST['id'];}

	$current_basket = $_SESSION['basket'];
	$basket_array = explode(",", $current_basket);

	if (isset($_SESSION['basket']) && !(array_search($id, $basket_array) > -1)){
		array_push($basket_array, $id);
		$_SESSION['basket'] = implode(",", $basket_array);
	}else if(!(array_search($id, $basket_array) > -1)){
		$_SESSION['basket'] = $id;
	}
}
else if ($p == "getBasketInfo")
{
	if (isset($_SESSION['basket'])){
		echo $_SESSION['basket'];
	}else{
		echo "";
	}
}
else if ($p == "removeBasketInfo")
{
	if (isset($_POST['id'])){$id = $_POST['id'];}
	if (isset($_SESSION['basket'])){
		$current_basket = $_SESSION['basket'];
		$basket_array = explode(",", $current_basket);
		$key = array_search($id, $basket_array);
		unset($basket_array[$key]);
		if(!empty($basket_array)){
			$_SESSION['basket'] = implode(",", $basket_array);
		}else{
			unset($_SESSION['basket']);
		}
	}
}
else if ($p == "flushBasketInfo")
{
	unset($_SESSION['basket']);
}
else if ($p == "sendWKey")
{
	if (isset($_POST['wkey'])){$wkey = $_POST['wkey'];}
	$_SESSION['wkey'] = $wkey;
}
else if ($p == "getWKey")
{
	if (isset($_SESSION['wkey'])){
		echo $_SESSION['wkey'];
	}else{
		echo "";
	}
}
else if ($p == 'flushWKey')
{
	unset($_SESSION['wkey']);
}
else if ($p == 'tableToggle')
{
	if (isset($_GET['table'])){$table = $_GET['table'];}
	if(!isset($_SESSION['ngs_'.$table])){
		$_SESSION['ngs_'.$table] = 'extend';
		echo "extend";
	}else if($_SESSION['ngs_'.$table] == ''){
		$_SESSION['ngs_'.$table] = 'extend';
		echo "extend";
	}else{
		$_SESSION['ngs_'.$table] = '';
		echo "";
	}
}
else if ($p == 'getTableToggle')
{
	if (isset($_GET['table'])){$table = $_GET['table'];}
	if(!isset($_SESSION['ngs_'.$table])){
		$_SESSION['ngs_'.$table] = '';
		echo "";
	}else if($_SESSION['ngs_'.$table] == ''){
		echo "";
	}else{
		echo "extend";
	}
}
else if ($p == 'setPlotToggle')
{
	if (isset($_GET['type'])){$type = $_GET['type'];}
	if (isset($_GET['file'])){$file = $_GET['file'];}
	unset($_SESSION['plot_type']);
	$_SESSION['plot_type'] = $type;
	if($_SESSION['plot_type'] == 'generated'){
		unset($_SESSION['plot_file']);
		$_SESSION['plot_file'] = $file;
	}else{
		unset($_SESSION['plot_file']);
		$_SESSION['plot_file'] = '';
	}
}
else if ($p == 'getPlotToggle')
{
	if($_SESSION['plot_type'] == 'generated'){
		echo $_SESSION['plot_file'];
	}else{
		echo '';
	}
}

exit;
?>
