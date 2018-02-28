<?php
// THIS CODE IS FOR SEARCHING ALL 
// IT ALSO INCLUDES SOME TOOLS FOR AVOIDING PRINTING UNNECESSARY THINGS

ini_set('display_errors', 1);
error_reporting(E_ALL);

//load global vars and includes
include "select_species.inc.php";

//Store input data in $_SESSION to reaload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;

$array; // contains everything
$items=array(); // the interesting keys of array[SpecieX]
$something_printed=0;

// Loop through species
foreach ($Species as $Specie){
    $info=array(); // information for the search

    // Get information about your query in your specie:
    
    $sql = "SELECT g.gene_recommended_name, p.prot_recommended_name, gsyn.name_genesynonym, psyn.name_proteinsynonym, g.id_ENTREZGENE, p.id_Uniprot "
        . "FROM Gene g, Species sp, GeneSynonyms gsyn, ProteinSynonyms psyn, Proteins p "
        . "WHERE g.tax_id = sp.tax_id AND g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot "
        . "AND sp.common_name like '%".$Specie."%' AND (g.gene_recommended_name = '".$query."' OR gsyn.name_genesynonym = '".$query."' OR g.id_ENTREZGENE = '".$query."' OR psyn.name_proteinsynonym = '".$query."' OR p.prot_recommended_name = '".$query."' OR p.id_Uniprot like '".$query."\_%');";       
    
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli); 
         
    $GeneSynonyms = array();
    $ProteinSynonyms = array();
    $geneRecName=array();
    $protRecName=array(); 
    $GeneID= array();      
    $ProteinID=array();
    $FinalGeneOrthologues = array();
    $Pfam=array();
    $Pfam['ID'] = array(); $Pfam['similar_proteins'] = array();

    $GO_terms= array(); // an array cointaining the GO decription and type of this gene. 
    $GO_terms['C'] = array(); $GO_terms['F'] = array(); $GO_terms['P'] = array(); 
    $GO_similar_genes= array(); // an array cointaining arrays of genes with similar function
    $GO_similar_genes['C'] = array(); $GO_similar_genes['F'] = array(); $GO_similar_genes['P'] = array(); 
    
    // Save an empty array if the search is empty
    if (!mysqli_num_rows($rs)) { 
        $info["Gene recommended name"]=$geneRecName;
        $info["Protein recommended name"]=$protRecName;
        $info["Gene synonyms"]=$GeneSynonyms;
        $info["Protein synonyms"]=$ProteinSynonyms;
        $info["Phylogenetically related genes"]=$FinalGeneOrthologues;
        $info["GO terms"]=$GO_terms;
        $info["GO_similar_genes"]=$GO_similar_genes;
        $info["Pfam"]=$Pfam;
                
        //// AQUÍ FALTEN LA RESTA D'ITEMS !!!!!! ORTOLEGS ET AL
        $array[$Specie]= $info;

        continue; // go to the next specie
    }
    elseif (isset($_SESSION['queryData']['RecName']) or isset($_SESSION['queryData']['Synonyms']) or isset($_SESSION['queryData']['Orthologues']) or isset($_SESSION['queryData']['Pfam']) or isset($_SESSION['queryData']['GO'])){
         $something_printed = 1;        
    }
     
    // save things of the initial search 
    $i=0;
    while ($rsF = mysqli_fetch_array($rs)) {
        if ($i===0){ 
            $geneRecName[] = $rsF['gene_recommended_name'];
            $protRecName[] = $rsF['prot_recommended_name'];
            $GeneID[] = $rsF['id_ENTREZGENE'];      
            $ProteinID[] = $rsF['id_Uniprot']; 
            $i++;
        }
        
        $GeneSynonyms[] = $rsF['name_genesynonym'];
        $ProteinSynonyms[] = $rsF['name_proteinsynonym'];
        
    }     
    $GeneSynonyms=  array_unique($GeneSynonyms);
    $ProteinSynonyms=  array_unique($ProteinSynonyms);
    
    
    // add things to $info and $items if selected
      
    //RECCOMENDED NAMES:  
    if(isset($_SESSION['queryData']['RecName'])){  
        $info["Gene recommended name"]=$geneRecName;
        $items[]="Gene recommended name";
        
        $info["Protein recommended name"]=$protRecName;
        $items[]="Protein recommended name";                 
    }
    
    // SYNONYMS   
    if(isset($_SESSION['queryData']['Synonyms'])){
        $info["Gene synonyms"]=$GeneSynonyms;
        $items[]="Gene synonyms";
        
        $info["Protein synonyms"]=$ProteinSynonyms;
        $items[]="Protein synonyms";       
    }  
    
    // GENE ONTOLOGY
    if(isset($_SESSION['queryData']['GO'])){   
        
        // get the GO terms of this protein and the IDs of the similar GOs.
        
        $sql = "SELECT  GO.name, GO.type, sGO.id_Uniprot_similar, sGO.Type_GO FROM GeneOntology GO, Proteins_has_GeneOntology PhGO, Proteins P, Similar_GO sGO ".
               "WHERE GO.id_GO = PhGO.GeneOntology_id_GO AND PhGO.Proteins_id_Uniprot = P.id_Uniprot AND P.id_Uniprot = sGO.id_Uniprot ".
               "AND P.id_Uniprot = '".$ProteinID[0]."'";
        
        $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli); 
        
        $Similar_Proteins = array(); // array of the id_Uniprot of the proteins with similar GO terms 
        $Similar_Proteins['C'] = array(); $Similar_Proteins['F'] = array(); $Similar_Proteins['P'] = array();       
                
        while ($rsF = mysqli_fetch_array($rs)){   
            
            if($rsF['name'] != "-"){
                $type = $rsF['type'];
                $GO_terms[$type][] = $rsF['name'];
            }
            $Type_SimilarProtein = $rsF['Type_GO'];
            $Similar_Proteins[$Type_SimilarProtein][] = $rsF['id_Uniprot_similar'];                
        }

        foreach (array('C','F','P') as $type){
            $Similar_Proteins[$type] = array_unique($Similar_Proteins[$type]);
            $GO_terms[$type] = array_unique($GO_terms[$type]);
        }
        
        // generate the array that cointains the gene names of all the similar genes $GO_similar_genes (contains id_Uniprot and gene name):
        
        foreach (array('C','F','P') as $type){
            foreach ($Similar_Proteins[$type] as $Prot_ID){
            
                // get the gene name
                $sql = "SELECT G.gene_recommended_name FROM Gene G, Proteins P WHERE G.id_ENTREZGENE = P.id_ENTREZGENE AND P.id_Uniprot = '".$Prot_ID."'";
                $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli);
                $gene_name = mysqli_fetch_row($rs)[0];
                
                if ($gene_name != "-"){
                    $GO_similar_genes[$type][] = $gene_name.";".$Prot_ID;
                }
                              
            }
        } 
        
        // add to $info
        $info["GO terms"]=$GO_terms;
        $info["GO similar genes"]=$GO_similar_genes;
        $items[]="GO terms";
    }
    
                   
    // If you want to search for Orthologues
    if(isset($_SESSION['queryData']["Orthologues"])){
        
        //Start with an empty array
        $GeneOrthologues=array();
               
        // We have $GeneID to link the Gene and Orthologue cluster
        // Get information about the orthologue cluster in your GeneID: 
        $cluster_sql = "SELECT oc.ortho_cluster "
                . "FROM Gene g, OrthologueCluster oc, Gene_has_OrthologueCluster goc "
                . "WHERE g.id_ENTREZGENE = goc.Gene_id_ENTREZGENE "
                . "AND oc.ortho_cluster = goc.OrthologueCluster_ortho_cluster "
                . "AND g.id_ENTREZGENE = ".$GeneID[0].";";  
        
        // We look for the ortho cluster at the DB
        $cluster_rs = mysqli_query($mysqli, $cluster_sql) or print mysqli_error($mysqli); 

        //If there is no ortho cluster for the query gene, create empty array and end
        if (!mysqli_num_rows($cluster_rs)) { 
            //print("YOUR QUERY GENE HAS NO ORTHOLOGUE CLUSTER");
            $info["Phylogenetically related genes"]=$FinalGeneOrthologues; //empty array
        
            
        //If there is one or more ortho cluster for the query gene:
        } else {

            //Here we have an array with the different orthologue cluster names and search MySQL with this name
            // I DONT KNOW IF THIS WORKS!!!!!!
            // The idea is to loop through the array of ortho clusters
            $i=0;
            while ($cluster_rsF = mysqli_fetch_array($cluster_rs) and $i<10) {
                $i++;

                // Query MySQL DB now with a ortho cluster every time
                $ortho_sql = "SELECT g.gene_recommended_name, s.common_name "
                    . "FROM Gene g, Gene_has_OrthologueCluster goc, Species s "
                    . "WHERE g.id_ENTREZGENE = goc.Gene_id_ENTREZGENE "
                    . "AND g.id_ENTREZGENE != '$GeneID[0]' AND goc.OrthologueCluster_ortho_cluster = '$cluster_rsF[0]';";      

                $ortho_rs = mysqli_query($mysqli, $ortho_sql) or print mysqli_error($mysqli); 
            
                //If there are no results of the ortho cluster appart from original gene
                if (!mysqli_num_rows($ortho_rs)) { 
                    $info["Phylogenetically related genes"]=$GeneOrthologues; //empty array
                } else {
 
                    while ($ortho_rsF = mysqli_fetch_array($ortho_rs)) {
                        //I save here the info of the orthologue gene
                        $Ortho_Specie = $ortho_rsF['common_name'];
                        $Ortho_Specie_array = explode(" ", $Ortho_Specie);
                        $Ortho_Specie = substr($Ortho_Specie_array[0],0,1).". ".$Ortho_Specie_array[1]; // ." ".$cluster_rsF[0]
                                                
                        $GeneOrthologues[] = strtoupper($ortho_rsF['gene_recommended_name'])."_".$Ortho_Specie; //This is the gene recommended name of the orthologue gene
                    }  
                }
            }
        }

        //Save only unique orthologue names . This is an array of Name_Specie
        $GeneOrthologues=  array_unique($GeneOrthologues); 
        
        // This has to be formatted into an array of "Name; Specie1, Specie2, SpecieN"
        
        // create an associative array                
        $GeneOrthologues_associative = array();
        foreach($GeneOrthologues as $go){
            $name = explode("_",$go)[0];
            $specie = explode("_",$go)[1];
            
            if (!isset($GeneOrthologues_associative[$name])){
                $GeneOrthologues_associative[$name] = array();
            }
            else{
                $GeneOrthologues_associative[$name][] = $specie;
            }
        }
        
        // create the array of strings:
        $FinalGeneOrthologues = array();
        
        foreach($GeneOrthologues_associative as $name => $species){
            $FinalGeneOrthologues[] = $name."<br><b>found in</b> ".implode(", ",$species)."<br>";
        }
    
        // If I have found gene orthologues, I now save it into $info to print it later

        $info["Phylogenetically related genes"]=$FinalGeneOrthologues;
        $items[]="Phylogenetically related genes";

         
    }
    
    // BEFORE THIS IT IS ORTHOLOGUES
        // If you want to search for Similar proteins & Pfam domains
        // If you want to search for Orthologues
    if(isset($_SESSION['queryData']["Pfam"])){

        //Start with an empty array
        $Pfam=array();

        // We have $ProteinID to link the Proteins and Pfam
        // Get information about the id_pfam in your ProteinID:

        $pfam_sql = "SELECT pf.id_pfam, pf.name_pfam "
                . "FROM Pfam pf, Proteins p "
                . "WHERE pf.id_pfam = p.id_pfam "
                . "AND p.id_Uniprot = '$ProteinID[0]';";

        $pfam_rs = mysqli_query($mysqli, $pfam_sql) or print mysqli_error($mysqli);

        while ($rsF = mysqli_fetch_array($pfam_rs)){
            if($rsF['id_pfam'] != '-'){
                $id_pfam = $rsF['id_pfam'];
                $Pfam['ID'] = array($rsF['id_pfam'],$rsF['name_pfam']);
            }
        }

        $similarproteins_sql = "SELECT p.id_Uniprot from Proteins p, Gene g, Species s "
                . "WHERE p.id_ENTREZGENE = g.id_ENTREZGENE "
                . "AND g.tax_id = s.tax_id "
                . "AND p.id_pfam = '$id_pfam' "
                . "AND p.id_Uniprot != '$ProteinID[0]' "
                . "AND s.common_name like '%$Specie%';";


        $similarproteins_rs = mysqli_query($mysqli, $similarproteins_sql) or print mysqli_error($mysqli);
//
        while ($rsF = mysqli_fetch_array($similarproteins_rs)){
            $Pfam['similar_proteins'][] = $rsF['id_Uniprot'];
        }

        if ($Pfam){
                $something_printed = 1;
                $info["Pfam"]=$Pfam;
                $items[]="Pfam";

        }





    }

    // BEFORE THIS IT IS PFAM
    
    
    
    
    //Add to $array ;
    
    $array[$Specie]= $info;
     
} 
         
$items=  array_unique($items);


// PRINT 

?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top">

<!-- <div class="form-check">-->
    <input type="checkbox" id="select_all"/> Select All
<!--</div>-->
    
<?php
foreach ($items as $t) {
    
    
    print "<h1>".$t."</h1>"; //only if $array[$s][$t] is not empty
    ?> <div class="outline"><?php
           
    foreach ($Species as $s){
 
        if ($t=="GO terms"){     
                       
            $specie_printed = 0;
            
            foreach (array('C','F','P') as $type){
                
                if ($type=="C"){ $name = "Component";}
                if ($type=="F"){ $name = "Molecular Function";}
                if ($type=="P"){ $name = "Biological Process";}
                
                if (!empty($array[$s]["GO terms"][$type]) and (count($array[$s]["GO terms"][$type])>1 or  $array[$s]["GO terms"][$type][0]!="-")){
                    
                    if ($specie_printed===0){
                        ?> &nbsp;&nbsp;&nbsp;&nbsp; <?php
                        print " <h3> ".$s."</h3><br>";
                        $specie_printed=1;
                    }
                    
                    print "<h4>This gene has the following ".$name." GO terms:</h4><br>";
                    // print the GO term names
                    foreach($array[$s]["GO terms"][$type] as $term){
                        if ($term != "-"){
                            print "<p> &nbsp;&nbsp;&nbsp;&nbsp;".$term."</p>";
                        }
                    }  print "<br>";                                   
                
                    if (!empty($array[$s]["GO similar genes"][$type]) and (count($array[$s]["GO similar genes"][$type])>1 or  $array[$s]["GO similar genes"][$type][0]!="-")){                
                        // print the similar genes:
                        print "<h4>Genes with a similar ".$name." in this specie are...</h4><br>";
                        foreach($array[$s]["GO similar genes"][$type] as $sim_gene){

                            $gene_rec_name = explode(";",$sim_gene)[0];
                            if ($gene_rec_name != "-"){
                            ?>   
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="check_good" value="" id="final_pubmed" name="pubmed_query[<?php print $gene_rec_name ?>]">                                       
                                <?php print $gene_rec_name; //only if not "-" ?>     
                                <br>
                            <?php
                            }
                        }  
                    }                               
                }                                
            }           
        }
        
        elseif ($t=="Pfam"){
          $specie_printed = 0;
          if (!empty($array[$s]['Pfam']['ID'][0]) and (count($array[$s]['Pfam']['ID'])>1 or  $array[$s]['Pfam']['ID'][0]!="-")){

              if ($specie_printed===0){
                  print "<br><h4>".$s."</h4><br>";
                  $specie_printed=1;
              }

//            print_r($array[$Specie]['Pfam']['ID']); #IMPORTANT
            print "<h4>This protein has the following PFAM domains:</h4><br>";

            foreach(explode("|",$array[$s]['Pfam']['ID'][1]) as $pfam_names){
                    if($pfam_names != "-"){
                        ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="check_good" value="" id="final_pubmed" name="pubmed_query[<?php print $pfam_names ?>]">
                                <?php print $pfam_names; //only if not "-" ?>
                                <br>
                            <?php

//                        print($pfam_names);
                }
            }
            print "<h4>This protein has the following similar proteins:</h4><br>";

            foreach($array[$s]['Pfam']['similar_proteins'] as $simprots){
                
                $simprots = explode("_",$simprots)[0];

                ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="check_good" value="" id="final_pubmed" name="pubmed_query[<?php print $simprots ?>]">
                                <?php print $simprots; //only if not "-" ?>
                                <br>
                            <?php
//
//            $list=implode(",", $array[$Specie]['Pfam']['similar_proteins']);
//            print($list);
            }
          }
        }
        
        // other elseifs
        else{          
            
            
            
            // print specie header:
            
            if (!empty($array[$s][$t]) and (count($array[$s][$t])>1 or $array[$s][$t][0]!="-")){     
                print "<br><h3>".$s."</h3><br>";
            }
            
            foreach ($array[$s][$t] as $final){ ?>

                <?php if ($final == "-"){  continue; } ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" class="check_good" value="" id="final_pubmed" name="pubmed_query[<?php print explode("<br>",$final)[0] ?>]">                
                <?php print $final; //only if not "-" ?>  
                <br>

            <?php    
            }
        }
    }
    ?>
    </div>
<?php

}?>
                   
    <button type="submit" class="btn btn-primary">Submit</button>
</form>    

<script>
var select_all = document.getElementById("select_all"); //select all checkbox
var checkboxes = document.getElementsByClassName("check_good"); //checkbox items


select_all.addEventListener("change", function(e){
    for (i = 0; i < checkboxes.length; i++) { 
        checkboxes[i].checked = select_all.checked;
    }
});


for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].addEventListener('change', function(e){ //".checkbox" change 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(this.checked == false){
            select_all.checked = false;
        }
        //check "select all" if all checkbox items are checked
        if(document.querySelectorAll('.checkbox:checked').length == checkboxes.length){
            select_all.checked = true;
        }
    });
}
</script>    


<?php
print "<br>";
print footerDBW();   
    
// Debug if you didn't find anything
if ($something_printed===0){
    print(" <h4> Your search gave no results <br><br></4>");?>

<p><button class="btn btn-primary" onclick="window.location.href='index.php?new=1'">New Search</button></p>

    <?php
  
}
?>

