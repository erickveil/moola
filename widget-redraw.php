<?php
// widget-redraw.php
//
// Erick Veil
// 2013-04-10
//
// A switch that calls various widget "drawing" functions, which return their 
// html as a string. This script is called by an ajax request, and the 
// resulting string is handed back to it, where it is usualy used to dynamicaly 
// (re)draw a section of the page, using the same php-generated html that was 
// initialy used to draw the page on load.
//
// Values are passed by GET. func defines the function to call. Other 
// parameters are function-specific.
//

include "moola-modules.php";
//include "common-lib.php";

$func=$_GET['func'];

switch($func)
{
    case "redrawLedger":
        $range=Array('min'=>$_GET['min'], 'max'=>$_GET['max']);
        $hook_id=$_GET['hook'];
        redrawLedger($range,$hook_id);
        break;
    default:
        echo "unrecognized widget-redraw function.";
        break;
}

// 0.1
function redrawLedger($range,$hook_id)
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";
    $db_login=Array(
        "loc"=>$location,
        "usr"=>$user,
        "pw"=>$password,
        "db"=>$database);
    $start_bal=getPriorBalance($db_login,$range['min']);

    echo drawLedger($db_login,$start_bal,$range,$hook_id);
}

// 0.2.1
// This validation could be a little better
function validateUploadedFile($_FILE,$_POST)
{
    return true;
}

