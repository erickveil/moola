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
drawCSS();
drawHTMLBody();
drawHTMLFoot();


function drawHTMLHead()
{
}

function drawCSS()
{
}

function drawHTMLBody()
{
    $location="localhost";
    $user="moola";
    $password="password";
    $database="moola";

    echo "<body>";
    drawLedger($location,$user,$password,$database);
    echo "</body>";
}

function drawHTMLFoot()
{
}

