<?php

// moola.php
//
// Erick Veil
// 2013-04-07
// 
// Personal Accounting Report page.
//
// Requires LAMP environment.
// simlink required in /var/www/html
//

include "moola-modules.php";

drawHTMLHead();
drawHTMLBody();
drawHTMLFoot();


// 0.1
function drawHTMLHead()
{
    echo "
        <html>
        <head>
    ";

    drawCSS();

    echo "</head>";

}

// 0.1.1
function drawCSS()
{
    echo "<link rel='stylesheet' type='text/css' href='styles.css' />";
}

// 0.2
function drawHTMLBody()
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";
    $range=getDefaultDateRange();

    echo "<body>";
    echo drawLedger($location,$user,$password,$database,0,$range);
    echo "</body>";
}

// 0.2.1
function getDefaultDateRange()
{
    $tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
    $lastyear=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-1));
    $range=array("min"=>$lastyear, "max"=>$tomorrow);
    print_r($range);
    return $range;
}

// 0.3
function drawHTMLFoot()
{
    echo "</html>";
}

