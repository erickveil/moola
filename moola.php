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
drawHTMLBody($_POST);
drawHTMLFoot();


// 0.1
function drawHTMLHead()
{
    echo "
        <html>
        <head>
    ";

    drawCSS();
    drawScripts();

    echo "</head>";
}

// 0.1.1
function drawCSS()
{
    $jqui_css_path="jquery-ui-1.10.2/themes/base/";
    $jqui_css_base=$jqui_css_path."jquery.ui.all.css";

    echo "<link rel='stylesheet' type='text/css' href='styles.css' />
        <!--suppress HtmlUnknownTarget -->
<link rel='stylesheet' type='text/css' href='${jqui_css_base}' />";
}

// 0.2
function drawHTMLBody($_POST)
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

    $start_bal=getPriorBalance($db_login,$range["min"]);
    
    echo "<body>";

    echo drawState($_POST);

    echo drawImportDialog();

    echo drawControls();

    echo "<div class='hook' id='${hook_id}'>";
    echo drawLedger($db_login,$start_bal,$range,$hook_id);
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

// 0.2.2
function drawState($_POST)
{
    $state='';

    $html_str="<input type='hidden' id='state' value='${state}'>";

    return $html_str;
}

// 0.3
function drawHTMLFoot()
{
    echo "</html>";
}

