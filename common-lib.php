<?php

// common-lib.php
//
// Erick Veil
// 2013-04-06
//
// A collection of commonly available functions for moola
//

function handleError($err_string,$mysqli)
{
    echo $err_string."\n";
    $mysqli->close();
    exit();
}

function loadMySqli($location,$user,$password,$database)
{
    $mysqli=mysqli_connect($location,$user,$password,$database);
    if(mysqli_connect_erno())
     {
         $sql_err=mysqli_connect_error();
         handleError("drawLedger module failed to connect to database:
         $sql_err",$mysqli);
     }
     return $mysqli;
}
