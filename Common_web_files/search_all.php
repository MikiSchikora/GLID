<?php

// THIS CODE IS FOR CHECKING RECCOMENDED NAME

ini_set('display_errors', 1);
error_reporting(E_ALL);

//load global vars and includes
include "select_species.inc.php";

//Store input data in $_SESSION to reaload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;
//If I want to redirect the query to another script
//if ($_REQUEST['myQuery']) {
//    header('Location: search.php?myQuery=' . $_REQUEST['myQuery']);
//}

// Loop through species
foreach ($Species as $Specie){

    $geneRecName = Null;
    $protRecName = Null;

    // Get information about your query in your specie:
    
    $sql = "SELECT g.gene_recommended_name, p.prot_recommended_name, gsyn.name_genesynonym, psyn.name_proteinsynonym, g.id_ENTREZGENE, p.id_Uniprot "
        . "FROM Gene g, Species sp, GeneSynonyms gsyn, ProteinSynonyms psyn, Proteins p "
        . "WHERE g.tax_id = sp.tax_id AND g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot "
        . "AND sp.common_name like '%".$Specie."%' AND (g.gene_recommended_name = '".$query."' OR gsyn.name_genesynonym = '".$query."' OR g.id_ENTREZGENE = '".$query."' OR psyn.name_proteinsynonym = '".$query."' OR p.prot_recommended_name = '".$query."' OR p.id_Uniprot = '".$query."');";       
    
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli); 
         
    $GeneSynonyms = array();
    $ProteinSynonyms = array();
    
    while ($rsF = mysqli_fetch_array($rs)) {
        $geneRecName = $rsF['gene_recommended_name'];
        $protRecName = $rsF['prot_recommended_name'];
        $GeneSynonyms[] = $rsF['name_genesynonym'];
        $ProteinSynonyms[] = $rsF['name_proteinsynonym'];
        $GeneID = $rsF['id_ENTREZGENE'];      
        $ProteinID = $rsF['id_Uniprot'];               
    } 
    
    //RECCOMENDED NAMES:
    
    if(isset($_REQUEST['RecName'])){  
        
        if ($protRecName or $geneRecName){
            $something_printed = 1;
            print(" <h3> $Specie </h3> ");      
            if ($geneRecName){ print(" <h5> Gene Recommended Name: $geneRecName <br></h5> ");}
            if ($protRecName){ print(" <h5> Protein Recommended Name: $protRecName <br></h5> ");}
        }
    
    }
    
    if(isset($_REQUEST['Synonyms'])){
        
        print "here go synonyms";
        
    }  
} 

// Debug if you didn't find anything
if (!$something_printed){
    print(" <h5> Your search gave no results </5>");
}