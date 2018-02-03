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


// Define the type of query:    
$query = $_REQUEST['myQuery'];
$type_query = "";
$all_checked = 0;
while($all_checked == 0){
    // search database for gene_recommended_name
    $sql = "SELECT g.gene_recommended_name from Gene g WHERE g.gene_recommended_name = '".$query."'";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
    if (mysqli_num_rows($rs)){ $type_query = "gene"; break;}

    // search database for protein_recommended_name
    $sql = "SELECT p.prot_recommended_name from Proteins p WHERE p.prot_recommended_name = '".$query."'";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
    if (mysqli_num_rows($rs)){ $type_query = "prot"; break;}  

    // search database for gene_synonyms
    $sql = "SELECT gsyn.name_genesynonym from GeneSynonyms gsyn WHERE gsyn.name_genesynonym = '".$query."'";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
    if (mysqli_num_rows($rs)){ $type_query = "gsyn"; break;}  
    
    // search database for prot_synonyms
    $sql = "SELECT psyn.name_proteinsynonym from ProteinSynonyms psyn WHERE psyn.name_proteinsynonym = '".$query."'";
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);  
    if (mysqli_num_rows($rs)){ $type_query = "psyn"; break;}
        
    $all_checked = 1;
}
    
if ($all_checked == 1){
    print(" <h4> Your search gave no results, try again... </h4> ");
    exit();    
}