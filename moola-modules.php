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
    $mysqli=loadMySqli($location,$user,$password,$database);

    $sql="select DATE, ".
        "AMOUNT, ".
        "SERIAL, ".
        "COMMENTS ".
        "from downloads ";
    $result_obj=$mysqli->query($sql);
    if($result_obj===false)
    {
        handleError("Query failed: $sql\n".$msqli->error,$mysqli);
    }

    // build ledger string
    $html_str="
        <div class='ledger_widget' > 
    ";
    while($row=$result_obj->fetch_assoc())
    {
        $html_str.="
            <div class='ledger_entry' >".

            "<span class='ledger_date' >".
            $row["DATE"]."
            </span>".
            
            "<span class='ledger_serial' >".
            $row["SERIAL"].
            "</span>".

            "<span class='ledger_amount' >".
            $row["AMOUNT"]."
            </span>".

            "<span class='ledger_com' >".
            $row["COMMENTS"].
            "</span>".

            "</div>";

    }
    $html_str.="</div>";
    $result_obj->free();

    return $html_str;
}

echo drawLedger("localhost","moola","password","moola");
