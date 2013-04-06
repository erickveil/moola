#!/usr/bin/php
<?php
//
// moola.php
// 
// Erick Veil
// 2013-03-31
//
// Converts csv files of a specific format into an sql table.
//

// the log-in information might later be configurable
$location="localhost";
$user="moola";
$password="password";
$database="moola";
$mysqli=mysqli_connect($location,$user,$password,$database);
if(mysqli_connect_errno())
{
    $sql_err=mysqli_connect_error();
    handleError("Failed to connect to database: $sql_err");
}

// the top two rows of First Northern CSV files are a title, and col heads.
$skip=2;

// one parameter should be path to csv:
$filename=$argv[1];

$valid=explode(".",$filename);
if($valid[count($valid)-1]!="csv")
{
    $type=mime_content_type($filename);
    handleError("The import file is not csv. Mimetype: ${type}");
}

$flags=FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES;

$filerows=file($filename,$flags);
if($filerows===false)
{
    handleError("The argument is not a valid file.");
}

importData($filerows,$skip,$mysqli);

//end
mysqli_close($mysqli);

// 0.1.0
function handleError($err_string)
{
    exit($err_string."\n");
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

        $sql="insert into downloads".
            "values (";
        for($i=0;i<count($fields);++$i)
        {
            $sql.=($fields[$i].", ");
        }
        $sql.=")";

        mysqli_query($mysqli,$sql);
    }
}

// 0.2.1
function fixDate($date)
{
    $components=explode("/",$date);
    $month=$components[0];
    $day=$components[1];
    $year=$components[2];
    $date="${year}-${month}-${day}";
    return $date;
}

