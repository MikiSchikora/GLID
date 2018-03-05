<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "globals.inc.php";

$_SESSION['queryData'] = $_REQUEST;

// This script appends the comment of the user to Comments.tbl

$myfile = fopen("Comments.tbl", "a") or die("Unable to open file!");
$txt = $_SESSION['queryData']['InputEmail']."\t".$_SESSION['queryData']['suggestions'];
fwrite($myfile, "\n". $txt);
fclose($myfile);


print headerDBW("Home - GLID project");

print "<br><br><br><br><h3>Your suggestion has been processed, we'll email you back in three days as maximum.</h3>";

print footerDBW();
