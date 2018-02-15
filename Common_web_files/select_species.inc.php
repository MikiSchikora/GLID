<?php

//THIS CODE CAN BE INCLUDED ON TOP OF ANY OTHER search_*.php

include "globals.inc.php";

// Print the navigation bar:
print headerDBW($_REQUEST['myQuery']); 

// DEFINE $Species to look at
$spec_specie = $_REQUEST['mySpecie'];
$spec_taxonomy = $_REQUEST['myTaxonomy'];
// Debug:
if ($spec_specie and $spec_taxonomy){
    print "<h4> <br> You can't look for a both a specific specie and a taxonomic group , TRY AGAIN :)</h4>";
    exit();    
}
// Look at only one specie:
if ($spec_specie){
    $Species = array($spec_specie);    
}
// Look at a taxonomic group
elseif ($spec_taxonomy) {
    $Taxonomies = array($spec_taxonomy);
    // add tree dependencies between taxonomies:
    if ($spec_taxonomy=="Vertebrates"){
        $Taxonomies[] = "Mammals";
        $Taxonomies[] = "Primates";
        $Taxonomies[] = "Rodents";
        
    }
    if ($spec_taxonomy=="Mammals"){
        $Taxonomies[] = "Primates";
        $Taxonomies[] = "Rodents";
    }

    // Add to species:
    $Species = array();
    foreach ($Taxonomies as $Tax){
    
        $sql = "SELECT common_name FROM Species s, Taxonomy tx "
            . "WHERE s.division_id = tx.division_id "
                . "AND tx.name_taxonomy = '".$Tax."'";
        $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
        while ($rsF = mysqli_fetch_array($rs)) {
            $Species[] = $rsF['common_name'];
        }
    }
}
else{
    $sql = "SELECT common_name FROM Species";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);    
     
    $Species = array();
    while ($rsF = mysqli_fetch_array($rs)) {
        $Species[] = $rsF['common_name'];
    }    

}

// If a specie has ', correct it:
$Final_species = array();
foreach ($Species as $s){
    $Final_species[] = str_replace("'","\'",$s);
}
$Species = $Final_species;


// Define the type of query (may be more than one):    
$query = $_REQUEST['myQuery'];
