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



// THE CODE BELOW GENERATES THE RECCOMENDED NAME OUTPUT
//First make sure that the option RecName has been selected in the form
if(isset($_REQUEST['RecName'])){   
    
    // Loop through species
    foreach ($Species as $Specie){
        
        $geneRecName = Null;
        $protRecName = Null;

        // Gene RecName
        $sql = "SELECT g.gene_recommended_name FROM Gene g, Species sp, GeneSynonyms gsyn, ProteinSynonyms psyn, Proteins p "
            . "WHERE g.tax_id = sp.tax_id AND g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot "
            . "AND sp.common_name like '%".$Specie."%' AND (g.gene_recommended_name = '".$query."' OR gsyn.name_genesynonym = '".$query."' OR g.id_ENTREZGENE = '".$query."' OR psyn.name_proteinsynonym = '".$query."' OR p.prot_recommended_name = '".$query."' OR p.id_Uniprot = '".$query."');";       
        $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
        if($rs){$geneRecName = mysqli_fetch_assoc($rs)['gene_recommended_name'];}

        // Protein RecName
        $sql = "SELECT p.prot_recommended_name FROM Gene g, Species sp, GeneSynonyms gsyn, ProteinSynonyms psyn, Proteins p "
            . "WHERE g.tax_id = sp.tax_id AND g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot "
            . "AND sp.common_name like '%".$Specie."%' AND (g.gene_recommended_name = '".$query."' OR gsyn.name_genesynonym = '".$query."' OR g.id_ENTREZGENE = '".$query."' OR psyn.name_proteinsynonym = '".$query."' OR p.prot_recommended_name = '".$query."' OR p.id_Uniprot = '".$query."');";       
        $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
        if($rs){$protRecName = mysqli_fetch_assoc($rs)['prot_recommended_name'];}

        
        
        if ($protRecName or $geneRecName){
            $something_printed = 1;
            print(" <h3> $Specie </h3> ");      
            if ($geneRecName){
                print("<h5><b>Gene Recommended Name <br></h5></b>");
               ?>
               <form name="MainForm" id="mainform-id" autocomplete="off" action="" method="POST" enctype="multipart/form-data" class="margin-top">
                   <div class="form-check">
                       <input class="form-check-input" type="checkbox" value="" id="query_gene_RecName" name="query_gene_RecName">
                       <label class="form-check-label" for="defaultCheck1">
                           <?php 
                          print(" <h5>$geneRecName <br></h5> ");
                           ?>
                       </label>
                   </div>
               <?php }
            
            if ($protRecName){ 
                print(" <h5><b> Protein Recommended Name <br></h5></b>");
                ?>
                <form name="MainForm" id="mainform-id" autocomplete="off" action="" method="POST" enctype="multipart/form-data" class="margin-top">
                   <div class="form-check">
                       <input class="form-check-input" type="checkbox" value="" id="query_prot_RecName" name="query_prot_RecName">
                       <label class="form-check-label" for="defaultCheck1">
                           <?php 
                          print(" <h5>$protRecName <br></h5> ");
                           ?>
                       </label>
                   </div>
            <?php }
        }
    }    
    
    // You may not find anything in the selected specie, but it is found in another specie
    if(!isset($something_printed)){
        print "<h4> <br> Your search gave no results </h4>";       
    }
}
else {print "<h4> <br> Recommended name not selected </h4>";}
?>
