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
$func=$_GET['func'];

switch($func)
{
    case "redrawLedger":
        $min=$_GET['min'];
        $max=$_GET['max'];
        $hook_id=$_GET['hook'];
        echo redrawLedger($min,$max,$hook_id);
        break;
    default:
        echo "unrecognized widget-redraw function.";
        break;
}

// 0.1
function redrawLedger($min_date,$max_date,$hook_id)
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";
    $range=Array("min"=>$min_date, "max"=>$max_date);
    $db_login=Array(
        "loc"=>$location,
        "usr"=>$user,
        "pw"=>$password,
        "db"=>$database);

    echo drawLedger($db_login,0,$range,$hook_id);
}
