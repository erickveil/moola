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


function drawHTMLHead()
{
    echo "
        <html>
        <head>
    ";

    drawCSS();

    echo "</head>";

}

function drawCSS()
{
    echo "<link rel='stylesheet' type='text/css' href='styles.css' />";
}

function drawHTMLBody()
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";

    echo "<body>";
    echo drawLedger($location,$user,$password,$database);
    echo "</body>";
}

function drawHTMLFoot()
{
    echo "</html>";
}

