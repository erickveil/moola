<?php
// queries.php
// Erick Veil
// 2013-04-24
//
// Ajax requested queries for moola
//
// must be invoked via URI as
// querries.php?func=function_name
// all other attributes depend on function_name
//

include "common-lib.php";

$func=$_GET['func'];

switch($func)
{
case "addEntry":
    echo addEntry($_GET);
    break;
default:
    echo "queries.php unrecognized function parameter.";
    exit();
}

// 0.1.0
function addEntry($entry)
{
    $db_login=getLoginData(); 

    $mysqli=loadMySqli(
        $db_login["loc"],
        $db_login["usr"],
        $db_login["pw"],
        $db_login["db"]);

    $sql="insert into downloads ".
        "(DATE,AMOUNT,SERIAL,COMMENTS,SOURCE) ".
        "values (".
        "\"".$entry['date']."\", ".
        $entry['amt'].", ".
        "\"".$entry['serial']."\", ".
        "\"".$entry['com']."\", ".
        "\"".$entry['src']."\");";

    $result=$mysqli->query($sql);
    if($result===false)
    {
        handleError("Insert failed: $sql\n".$mysqli->error,$mysqli);
        return "query failed";
    }
    return "all good";
}


