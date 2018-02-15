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


$lc_array = array_keys($_REQUEST['pubmed_query']);
$uc_array = array();
foreach ($lc_array as $lc){
    $uc_array[] = strtoupper($lc);
}
$final_query = array_unique($uc_array);

$_SESSION['queryPubmed'] = $final_query;


?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="query_pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top">
     
      
     <!-- <form name="MainForm" id="mainform-id" autocomplete="off" action="search_Synonims.php" method="POST" enctype="multipart/form-data" class="margin-top">
     -->
          
      <div class="form-group">
        <label>Add some extra keywords</label>
        <input type="text" class="form-control" id="query" name="Keywords" value="" placeholder= "Write here some extra keywords , delimited by ',' . Example: cancer, functional genomics "> <!-- value="<?php //print $_SESSION['queryData']['query'] ?>" -->
      </div>
     
     <button type="submit" class="btn btn-primary">Submit to PUBMED</button>
     
</form>
            
<?php           
            
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