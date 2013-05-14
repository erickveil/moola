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
case "getSource":
    $ptr=$_GET["ptr"];
    echo getSource($ptr);
    break;
default:
    echo "queries.php unrecognized function parameter.";
    exit();
}

// 0.1.0
function addEntry($entry)
{
    $converted_amt=fixAmount($entry['amt']);

    $mysqli=loadDB();

    // these entries have not received any sort of validation or massaging 
    // before this point and come from user input.
    $entry['serial']=$mysqli->real_escape_string($enry['serial']);
    $entry['com']=$mysqli->real_escape_string($enry['com']);

    $sql="insert into downloads ".
        "(DATE,AMOUNT,SERIAL,COMMENTS,SOURCE) ".
        "values (".
        "\"".$entry['date']."\", ".
        $converted_amt.", ".
        "\"".$entry['serial']."\", ".
        "\"".$entry['com']."\", ".
        "\"".$entry['src']."\");";

    $result=$mysqli->query($sql);
    if($result===false)
    {
        handleError("Insert failed: $sql\n".$mysqli->error,$mysqli);
        return "query failed";
    }
    $new_ptr=$mysqli->insert_id;
    return $new_ptr;
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

function dolarsToDouble($dollar_formatted)
{
    $search=array(" ","$",",");
    $formatted=str_replace($search,"",$dollar_formatted);
    return $formatted;
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

    $data=$mysqli->real_escape_string($data);

    if($field=="AMMOUNT")
        $data=fixAmount($data);

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

function getSource($ptr)
{
    $mysqli=loadDB();

    $sql="select SOURCE from downloads ".
        "where PTR = ${ptr};";

    $result=$mysqli->query($sql);
    if($result===false)
    {
        handleError("Source query failed: $sql\n".$mysqli->error,$mysqli);
        return "query failed";
    }

    // only expectin gone row per ptr
    $row=$result->fetch_assoc();
    return $row["SOURCE"];
}

