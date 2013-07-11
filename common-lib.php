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


function drawScripts()
{
    $jquery_ui_path="jquery-ui-1.10.2/ui/";
    
    $scriptlist=array(
        "jquery-1.9.1.min.js",
        $jquery_ui_path."jquery-ui.js",
        $jquery_ui_path."jquery.ui.core.js",
        $jquery_ui_path."jquery.ui.widget.js",
        $jquery_ui_path."jquery.ui.datepicker.js",
        $jquery_ui_path."jquery.ui.mouse.js",
        $jquery_ui_path."jquery.ui.draggable.js",
        $jquery_ui_path."jquery.ui.position.js",
        $jquery_ui_path."jquery.ui.resizable.js",
        $jquery_ui_path."jquery.ui.button.js",
        $jquery_ui_path."jquery.ui.dialog.js",
        "moola-control.js"
    );

    foreach($scriptlist as $script)
    {
        echo "<script src='".$script."' ></script>";
        echo "\n";
    }
}

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
         handleError("module failed to connect to database:
         $sql_err",$mysqli);
     }
     return $mysqli;
}

// for database querries with a running ballance, this gets the ballance up to
// before the querry.
function getPriorBalance($db_login,$date)
{
    $mysqli=loadMySqli(
    $db_login["loc"],
    $db_login["usr"],
    $db_login["pw"],
    $db_login["db"]);

    $sql="select sum(AMOUNT) ".
        "from downloads ".
        "where DATE < '${date}';";

    $result_obj=$mysqli->query($sql);
    if($result_obj===false)
    {
        handleError("Query failed: $sql\n".$mysqli->error,$mysqli);
    }
    
    $query_row=$result_obj->fetch_row();

    $ret=($query_row[0]==NULL)?"0":$query_row[0];
    return $ret;
}

// returns an associative array that contains the db login data
// for now it loads up the hard-coded data, but in the future, there will be a 
// variable set of data to obtain somehow.
function getLoginData()
{
    return array(
        "loc"=>"localhost",
        "usr"=>"moola",
        "pw"=>"password",
        "db"=>"moola");
}

// 0.2.4
// Amount should come in as currency, no dollar sign, a negative sign for 
// values under zero, rounded to the cent, with a decimal point. Whole dollars 
// that do not end in .00 are handled.
// returns cash value as an integer, representing total cents value of the 
// input
function fixAmount($amount)
{
    $split_amt=explode(".",$amount);
    if($split_amt[0]==$amount)
    {
        $amount.="00";
    }
    return str_replace(".","",$amount);    
}

