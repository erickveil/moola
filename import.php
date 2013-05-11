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
// 

include "common-lib.php";

// the log-in information might later be configurable
$location="localhost";
$user="moola";
$password="password";
$database="moola";
$mysqli=mysqli_connect($location,$user,$password,$database);
if(mysqli_connect_errno())
{
    $sql_err=mysqli_connect_error();
    handleError("Failed to connect to database: $sql_err",$mysqli);
}

// the top two rows of First Northern CSV files are a title, and col heads.
$skip=2;

// one parameter should be path to csv:
$filename=$argv[1];

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

        echo "executing: ${sql}\n";
        if($mysqli->query($sql)===true)
        {
            echo "insert success.\n";
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
// Amount should come in as currency, no dollar sign, a negative sign for 
// values under zero, rounded to the cent, with a decimal point. Whole dollars 
// that do not end in .00 are handled.
// returns cash value as an integer, representing total cents value of the 
// input
function fixAmount($amount)
{
    $split_amt=explode(".",$amount);
    if($split_amt==$ammount)
    {
        $ammount.="00";
    }
    return str_replace(".","",$amount);    
}

