<?php

// THIS CODE IS FOR CHECKING RECCOMENDED NAME

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

print headerDBW("Home - GLID project");

//Store input data in $_SESSION to reaload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;

if (isset($_FILES['json']['name'])){
    $my_json = file_get_contents($_FILES['json']['tmp_name']);
    $json_decoded = json_decode($my_json, true);
    $_SESSION['queryPubmed'] = $json_decoded;
}

else{

$lc_array = array_keys($_REQUEST['pubmed_query']);
$uc_array = array();
foreach ($lc_array as $lc){
    $uc_array[] = strtoupper($lc);
}
$final_query = array_unique($uc_array);

$_SESSION['queryPubmed'] = $final_query;
}

?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="query_pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top">
          
      <div class="form-group">
        <label>Add some extra keywords <b>If necessary</b></label>
        <input type="text" class="form-control" id="query" name="Keywords" value="" placeholder= "Write here some extra keywords in Google-like syntax"> <!-- value="<?php //print $_SESSION['queryData']['query'] ?>" -->
      </div>
     
     <button type="submit" class="btn btn-primary">Submit to PUBMED</button>
     
</form>
            
<?php           
            

print footerDBW();