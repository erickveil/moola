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
    // script paths defines in common-lib.php
    GLOBAL $jquery;
    GLOBAL $jquery_ui;
    GLOBAL $jqui_core;
    GLOBAL $jqui_widget;
    GLOBAL $jqui_date;

    echo "
        <html>
        <head>
    ";

    drawCSS();

    echo"
        <script src='${jquery}' ></script>
        <script src='${jquery_ui}' ></script>
        <script src='${jqui_core}' ></script>
        <script src='${jqui_widget}' ></script>
        <script src='${jqui_date}' ></script>
        <script src='moola-control.js' ></script>
    ";

    echo "</head>";

}

// 0.1.1
function drawCSS()
{
    GLOBAL $jqui_css_base;

    echo "<link rel='stylesheet' type='text/css' href='styles.css' />";
    echo "<link rel='stylesheet' type='text/css' href='${jqui_css_base}' />";
}

// 0.2
function drawHTMLBody()
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";
    $range=getDefaultDateRange();
    $db_login=Array(
        "loc"=>$location,
        "usr"=>$user,
        "pw"=>$password,
        "db"=>$database);
    $hook_id="hook_1";
    
    echo "<body>";
    echo "<div class='hook' id='${hook_id}'>";
    echo drawLedger($db_login,0,$range,$hook_id);
    echo "<div>";
    echo "</body>";
}

// 0.2.1
function getDefaultDateRange()
{
    $tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
    $lastyear=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-1));
    $range=array("min"=>$lastyear, "max"=>$tomorrow);
// print_r($range);
    return $range;
}

// 0.3
function drawHTMLFoot()
{
    echo "</html>";
}

