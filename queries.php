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
case "softDeleteEntry":
    echo softDelete($_GET);
    break;
case "editEntry":
    echo editEntry($_GET);
    break;
default:
    echo "queries.php unrecognized function parameter.";
    exit();
}

// 0.1.0
function addEntry($entry)
{
    $mysqli=loadDB();

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

// 0.1.1
function loadDB()
{
    $db_login=getLoginData(); 

    $mysqli=loadMySqli(
        $db_login["loc"],
        $db_login["usr"],
        $db_login["pw"],
        $db_login["db"]);

    return $mysqli;
}

// 0.2.0
function softDelete($entry)
{
    $mysqli=loadDB();

    $sql="update downloads ".
        "set DEL = \"".$entry["type"]."\" ".
        "where PTR = ".$entry["ptr"].";";

    $result=$mysqli->query($sql);
    if($result===false)
    {
        handleError("Soft Delete failed: $sql\n".$mysqli->error,$mysqli);
        return "query failed";
    }
    return "all good";
}

// 0.3.0
function editEntry($entry)
{
    $ptr=$entry["ptr"];
    $field=$entry["field"];
    $data=$entry["data"];

    $mysqli=loadDB();

    $sql="update downloads ".
        "set ${field} = \"${data}\" ".
        "where PTR = ${ptr};";

    $result=$mysqli->query($sql);
    if($result===false)
    {
        handleError("Update failed: $sql\n".$mysqli->error,$mysqli);
        return "query failed";
    }
    return "all good";
}

