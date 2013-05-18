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

    $sql="select ".
        "PTR, ".
        "DATE, ".
        "AMOUNT, ".
        "SERIAL, ".
        "COMMENTS ".
        "from downloads ".
        "where ".
        "(DATE between ".
        "'".$range["min"]."' and ".
        "'".$range["max"]."') ".
        "and ".
        "(DEL is null) ".
        "order by DATE";

    $result_obj=$mysqli->query($sql);
    if($result_obj===false)
    {
        handleError("Query failed: $sql\n".$mysqli->error,$mysqli);
    }

    // build ledger string
    $html_str="
        <div class='ledger_widget' form='widget' > 
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
    return number_format(($num/100),2,".",",");
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
    $post_draw_focus="\"\"";
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
            onclick='${function}(\"${hook_id}\",${post_draw_focus});' 
            value='Select Range'
        />
    ";

    $html_str.="</div>";

    return $html_str;
}

// 0.1.3
// draws the html form elements for one entry of the ledger, returning the string.
// $alternate is the class for the entry's div, which alternates classes. $row 
// is the sql query object for the row that the data comes from. $bal_class 
// is the class for the balance field, which depends on if it's positive or 
// negative, so that it receives different styles for each. $balance is the 
// balance value itself in cents.
function buildLedgerElements($alternate,$row,$bal_class,$balance)
{
    $id=$row["PTR"];

    // doesn't always catch the quotes, but it helps
    $src_quot=array("\"","'","\x98","\x99","\x8c","\x9d");
    $print_quot=array("&quot;","&apos;","&apos;","&apos;","&quot;","&quot;");
    $row["SERIAL"]=str_replace($src_quot,$print_quot,$row["SERIAL"]);
    $row["COMMENTS"]=str_replace($src_quot,$print_quot,$row["COMMENTS"]);

    $html_str="
    <div class='${alternate}' >".

    "<input type='text' field='ledger_date' id=${id} value='".
    $row["DATE"]."' />".
    
    "<input type='text' field='ledger_serial' id=${id} value='". 
    $row["SERIAL"]."' />".

    "<input type='text' field='ledger_amount' id=${id} value='".
    asCurrency($row["AMOUNT"])."' />".

    "<span type='text' field='${bal_class}' id=${id} >".
    asCurrency($balance).
    "</span>".

    "<input type='text' field='ledger_com' id=${id} value='".
    $row["COMMENTS"]."' />".

    "</div>";

    return $html_str;
}

// 0.2
function drawControls()
{
    $html_str="<div class='control_widget' form='widget' >"; 

    $html_str.="<input ". 
            "type='button' ".
            "value='Import CSV' ".
            "button='import' ".
        "/>";

    $html_str.="</div>";

    return $html_str;
}

