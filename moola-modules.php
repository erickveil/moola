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

// 0.1
function drawLedger($location,$user,$password,$database,$balance)
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

    $alternate="ledger_entry_1";
    while($row=$result_obj->fetch_assoc())
    {
        $balance+=$row["AMOUNT"];
        $bal_class=($balance>0)?"balance_gr":"balance_rd";

        if($alternate=="ledger_entry_2")
            $alternate="ledger_entry_1";
        else
            $alternate="ledger_entry_2";

        $html_str.="
            <div class='${alternate}' >".

            "<span class='ledger_date' >".
            $row["DATE"]."
            </span>".
            
            "<span class='ledger_serial' >".
            $row["SERIAL"].
            "</span>".

            "<span class='ledger_amount' >".
            asCurrency($row["AMOUNT"])."
            </span>".

            "<span class='${bal_class}' >".
            asCurrency($balance)."
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

// 0.1.1
function asCurrency($num)
{
    return number_format($num,2,".",",");
}

