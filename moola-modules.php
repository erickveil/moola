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
// range: [min]=a [max]=b, should be dates, pre-validated.
// dbLogin: [loc]=URI,[usr]=sql user name,[pw]=sql password, [db]=sql database
function drawLedger($db_login,$balance,$range,$hook_id)
{

    $date_range=drawDateRange("redrawLedger",$range,$hook_id);

    $mysqli=loadMySqli(
        $db_login["loc"],
        $db_login["usr"],
        $db_login["pw"],
        $db_login["db"]);

    $sql="select DATE, ".
        "AMOUNT, ".
        "SERIAL, ".
        "COMMENTS ".
        "from downloads ".
        "where DATE between ".
        "'".$range["min"]."' and ".
        "'".$range["max"]."' ".
        "order by DATE";

    $result_obj=$mysqli->query($sql);
    if($result_obj===false)
    {
        handleError("Query failed: $sql\n".$mysqli->error,$mysqli);
    }

    // build ledger string
    $html_str="
        <div class='ledger_widget' > 
    ";

    $html_str.=$date_range;
    $html_str.="<div class='ledger_fields' >";

    $alternate="ledger_entry_1";
    while($row=$result_obj->fetch_assoc())
    {
        $balance+=$row["AMOUNT"];
        $bal_class=($balance>0)?"balance_gr":"balance_rd";

        if($alternate=="ledger_entry_2")
            $alternate="ledger_entry_1";
        else
            $alternate="ledger_entry_2";

        $html_str.=buildLedgerElements($alternate,$row,$bal_class,$balance);
    }
    $html_str.="</div></div>";
    $result_obj->free();

    return $html_str;
}

// 0.1.1
function asCurrency($num)
{
    return number_format($num,2,".",",");
}

// 0.1.2
// DateRange sub-widget allows refining its parent component by a specific  
// date range. 
// the button executes the onclick event, which is passed as the string $function
// the paraneters of $function are always the values in the date range.
// and the id of the parent widget's hook
// requires inclusion of jquery, jquery-ui, 
// also provide a default range object ([min]=a, [max]=b)
function drawDateRange($function,$default,$hook_id)
{
    $html_str="<div class='date_ranger'>";

    $html_str.="
        <label for='min'>From </label>
        <input 
            type='text' 
            id='min' 
            name='min' 
            value='".$default['min']."'
        />

        <label for='max'> To </label>
        <input 
            type='text' 
            id='max' 
            name='max' 
            value='".$default['max']."'
        />

        <input 
            type='button' 
            id='get_range' 
            onclick='${function}(\"${hook_id}\");' 
            value='Select Range'
        />
    ";

    $html_str.="</div>";

    return $html_str;
}

// 0.1.3
function buildLedgerElements($alternate,$row,$bal_class,$balance)
{
    $html_str="
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

    return $html_str;
}

