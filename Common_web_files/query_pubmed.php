<?php

// THIS CODE IS FOR QUERYING PUBMED

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

$Names = $_SESSION['queryPubmed'];

if (!empty($_REQUEST['Keywords'])){
    $Keywords = $_REQUEST['Keywords'];
    $query = $Keywords." AND (\"".implode("\" OR \"",$Names)."\")";
}
else{
    $query = "(\"".implode("\" OR \"",$Names)."\")";
}

$final_query = str_replace(" ","+",$query);

header("Location: https://www.ncbi.nlm.nih.gov/pubmed/?term=".$final_query);


