<?php

// moola-modules.php
//
// Erick Veil
// 2013-04-06
//
// This is a library of on-screen modules, such as legers and graphs, for moola.
// Each module returns a string of html, so whever the module falls on the page
// helps define the layout. This way page widgets can be moved around the page
// easily.
//
// The return values are strings, so they can be passed back to xmlhttp requests
// from javascript, or used to lay out a pahe in php.
//

include "common-lib.php";

function drawLedger($location,$user,$password,$database)
{
    $mysqli=mysqli_connect($location,$user,$password,$database);
    if(mysqli_connect_erno())
     {
         $sql_err=mysqli_connect_error();
         handleError("drawLedger module failed to connect to database:
         $sql_err",$mysqli);
     }
}

