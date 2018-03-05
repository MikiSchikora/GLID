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
    $file_path = $_FILES['json']['tmp_name'];
    
    if ( 0 == filesize($file_path) ){
        print "<br><br><h3>You have to provide a non-empty file</h3>";
        exit(0);
    }
    
    $my_json = file_get_contents($file_path);
    
    $json_decoded = json_decode($my_json, true);
    if (count($json_decoded)==0){
        print "<br><br><h3>You have to provide a valid json file.</h3>";
        exit(0);       
    }
    
    $_SESSION['queryPubmed'] = $json_decoded;
}

else{
    
    if (!isset($_SESSION['queryData']['pubmed_query'])){
        print "<h3> You have to select something. <a href=\"./index.php\">Back to home</a> </h3>";
        exit(0);
    }

    $lc_array = array_keys($_REQUEST['pubmed_query']);
    $uc_array = array();
    foreach ($lc_array as $lc){
        $uc_array[] = strtoupper($lc);
    }
    $final_query = array_unique($uc_array);

    $_SESSION['queryPubmed'] = $final_query;
}

// Generate a string with the query names:

$Names_text = " AND (\"".implode("\" OR \"",$_SESSION['queryPubmed'])."\")";

?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="query_pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top" target="_blank">
          
      <div class="form-group" id="form-group">
        <label>Add some extra keywords <b>If necessary</b></label>
        <input type="text" class="form-control" id="query" name="Keywords" value="" placeholder= "Write here some extra keywords in PubMed-like syntax"> <!-- value="<?php //print $_SESSION['queryData']['query'] ?>" -->
      </div>
     
     <button type="submit" class="btn btn-primary">Submit to PUBMED</button>
     
</form>

<br><br>
<p>If you are not familiar with the PubMed-like keyword syntax <a href="https://www.youtube.com/watch?v=eEY1r_GDzcg" target="_blank">click here</a> for help</p>
<br><br>

<p><b>This is how your query will look like in PubMed</b></p>

<!-- Box with the keywords -->
<span class='printchatbox'></span>
<script type="text/javascript">
    $('.form-control').keyup(function(event) {
       newText = event.target.value;
       $('.printchatbox').text(newText);
    });
  
    var names = <?php print json_encode($Names_text); ?>;
    document.write(names);
</script>
            
<?php           
            

print footerDBW();