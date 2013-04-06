<?php

// common-lib.php
//
// Erick Veil
// 2013-04-06
//
// A collection of commonly available functions for moola
//

function handleError($err_string,$mysqli)
{
    echo $err_string."\n";
    $mysqli->close();
    exit();
}
