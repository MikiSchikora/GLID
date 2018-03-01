<?php

// THIS CODE IS FOR GETTING JSON FILE

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

//print headerDBW("Home - GLID project");

//Store input data in $_SESSION to reaload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;

$lc_array = array_keys($_REQUEST['pubmed_query']);
$uc_array = array();
foreach ($lc_array as $lc){
    $uc_array[] = strtoupper($lc);
}
$final_query = array_unique($uc_array);

$_SESSION['json'] = $final_query;
$final_query_json = json_encode($final_query);

header('Content-Disposition: attachment; filename=json_array.txt');
header('Content-Type: application/json');
echo $final_query_json;

?>
