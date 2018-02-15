<?php

// THIS CODE IS FOR CHECKING RECCOMENDED NAME

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

print headerDBW("Home - GLID project");

//Store input data in $_SESSION to reaload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;

//If I want to redirect the query to another script
//if ($_REQUEST['myQuery']) {
//    header('Location: search.php?myQuery=' . $_REQUEST['myQuery']);
//}

$query=Array();

$query=$_REQUEST;
print_r($query);

//if(isset($_REQUEST['pubmed'])){
//    $query=$_REQUEST['pubmed'];
//    print($query);
////    print "<a href=\"https://www.ncbi.nlm.nih.gov/pubmed/?term=$query\">Query Pubmed</a>";
////    print($query);  
////    print "<a href=\"https://www.ncbi.nlm.nih.gov/pubmed/?term=p53\">Query Pubmed</a>";
////    print($query);
//      
//}

print footerDBW();