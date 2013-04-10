<?php

// common-lib.php
//
// Erick Veil
// 2013-04-06
//
// A collection of commonly available functions for moola
//

// Global values

// jquery scripts are all referenced here so I only have to change them in one 
// place just reference these values for script includes.

$jquery="jquery-1.9.1.min.js";
$jquery_ui_path="jquery-ui-1.10.2/ui/";
$jquery_ui="${jquery_ui_path}jquery-ui.js";
$jqui_core="${jquery_ui_path}jquery.ui.core.js";
$jqui_widget="${jquery_ui_path}jquery.ui.widget.js";
$jqui_date="${jquery_ui_path}jquery.ui.datepicker.js";

$jqui_css_path="jquery-ui-1.10.2/themes/base/";
$jqui_css_base=$jqui_css_path."jquery.ui.all.css";

function handleError($err_string,$mysqli)
{
    echo $err_string."\n";
    $mysqli->close();
    exit();
}

function loadMySqli($location,$user,$password,$database)
{
    $mysqli=mysqli_connect($location,$user,$password,$database);
    if(mysqli_connect_errno())
     {
         $sql_err=mysqli_connect_error();
         handleError("drawLedger module failed to connect to database:
         $sql_err",$mysqli);
     }
     return $mysqli;
}
