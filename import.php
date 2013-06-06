#!/usr/bin/php
<?php
//
// import.php
// 
// Erick Veil
// 2013-03-31
//
// Converts csv files of a specific format into an sql table.
//
// 2013-05-20 Commented echo feedback in anticipation of script-level 
// invocation. 
// Modified script to run quietly, and able to be called on cli or uri.
// Modified it again to make it a library only
//
// All commented code in this script expires on 2013-07-20
// 

include "common-lib.php";

/*
if(isset($argv[1]))
{
    $filename=$argv[1];
}
 */

// 0.1.0
function runImport($filename)
{
    $login=getLoginData();

    $location=$login["loc"];
    $user=$login["usr"];
    $password=$login["pw"];
    $database=$login["dw"];

    $mysqli=loadMySqli($location,$user,$password,$database);

    // the top two rows of First Northern CSV files are a title, and col heads.
    $skip=2;
    
    $valid=explode(".",$filename);
    if($valid[count($valid)-1]!="csv")
    {
        $type=mime_content_type($filename);
        handleError("The import file is not csv. Mimetype: ${type}",$mysqli);
    }

    $flags=FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES;

    $filerows=file($filename,$flags);
    if($filerows===false)
    {
        handleError("The argument is not a valid file.",$mysqli);
    }

    importData($filerows,$skip,$mysqli);

    //end
    $mysqli->close();

    return true;
}

// 0.2.0
// pass an array of rowws, and the number from the top to skip
function importData($filerows,$skip,$mysqli)
{
    $count=0;
    foreach($filerows as $row)
    {
        ++$count;
        if($count<=$skip)
        {
            continue;
        }

        $fields=explode(",",$row);
        $fields[0]=fixDate($fields[0]);
        $fields[1]=fixAmount($fields[1]);
        $fields=fixQuotes($fields);
        $fields=fixEmptyComments($fields);

        if(isDuplicate($fields,$mysqli))
        {
            //echo "Duplicate found. Next.\n";
            continue;
        }

        $sql="insert into downloads".
            " (DATE,AMOUNT,SERIAL,DESCRIPTION,COMMENTS,SOURCE)".
            " values (";

        $d=", ";
        for($i=0;$i<count($fields);++$i)
        {
            if($i==count($fields)-1)
            {
                $d="";
            }
            $sql.=($fields[$i].$d);
        }
        $sql.=",\"download\")";

        //echo "executing: ${sql}\n";
        if($mysqli->query($sql)===true)
        {
            //echo "insert success.\n";
        }
        else
        {
            handleError($mysqli->error,$mysqli);
        }
    }
}

// 0.2.1
function fixDate($date)
{
    $components=explode("/",$date);
    $month=$components[0];
    $day=$components[1];
    $year=$components[2];
    $date="'${year}-${month}-${day}'";
    return $date;
}

// 0.2.2
function fixQuotes($fields)
{
    foreach($fields as $key=>$field)
    {
        if($field=="")
        {
            $fields[$key]="\"\"";
        }
    }
    print_r($fields);
    return $fields;
}

// 0.2.3
function fixEmptyComments($fields)
{
    $num=count($fields);
    if($num<5)
    {
        $missing=5-$num;
        for($i=0;$i<$missing;++$i)
        {
            $fields[]="\"\"";
        }
    }
    return $fields;
}

// 0.2.4
function isDuplicate($fields,$mysqli)
{
    $date=$fields[0];
    $amt=$fields[1];

    $sql="select DATE, AMOUNT from downloads where SOURCE = \"download\"";
    $result_obj=$mysqli->query($sql);

    if($result_obj===false)
    {
        handleError("Query failed: $sql\n".$mysqli->error,$mysqli);
    }

    while($row=$result_obj->fetch_assoc())
    {
        //echo $row["DATE"]." vs ".$date." and ".$row["AMOUNT"]." vs ".$amt."\n";
        if("'".$row["DATE"]."'"=="$date" && $row["AMOUNT"]==$amt)
        {
            $result_obj->free();
            return true;
        }
    }
    $result_obj->free();
    
    return false;
}

