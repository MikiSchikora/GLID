<?php

// THIS CODE IS FOR CHECKING RECCOMENDED NAME

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

//print headerDBW("Home - GLID project");

//Store input data in $_SESSION to reaload initial form if necessary
//$_SESSION['Keywords'] = $_REQUEST;

$Keywords = $_REQUEST['Keywords'];
$Names = $_SESSION['queryPubmed'];

$query = $Keywords." AND (\"".implode("\" OR \"",$Names)."\")";
$final_query = str_replace(" ","+",$query);

header("Location: https://www.ncbi.nlm.nih.gov/pubmed/?term=".$final_query);


//If I want to redirect the query to another script
//if ($_REQUEST['myQuery']) {
//    header('Location: search.php?myQuery=' . $_REQUEST['myQuery']);
//}


//print footerDBW();