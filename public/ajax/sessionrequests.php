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
        $_SESSION['basket'] .= ',' . $id;
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
        array_splice($basket_array, $key, 1);
        if(empty($basket_array)){
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

exit;
?>