<?php
// THIS CODE IS FOR SEARCHING ALL BUT INCLUDING GO TERMINOLOGY, NO ORTHOLOGUES
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
        . "AND sp.common_name like '%".$Specie."%' AND (g.gene_recommended_name = '".$query."' OR gsyn.name_genesynonym = '".$query."' OR g.id_ENTREZGENE = '".$query."' OR psyn.name_proteinsynonym = '".$query."' OR p.prot_recommended_name = '".$query."' OR p.id_Uniprot = '".$query."');";       
    
    $rs = mysqli_query($mysqli, $sql) or print mysqli_error($mysqli); 
         
    $GeneSynonyms = array();
    $ProteinSynonyms = array();
    $geneRecName=array();
    $protRecName=array(); 
    $GeneID= array();      
    $ProteinID=array();

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
        //$info["Orthologues"]=$ProteinSynonyms;
        $info["GO terms"]=$GO_terms;
        $info["GO_similar_genes"]=$GO_similar_genes;
        
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
    
    // ORTHOLOGUES GO HERE
    
    // PFAM GOES HERE
    
    // GENE ONTOLOGY
    if(isset($_SESSION['queryData']['Synonyms'])){   
        
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
                $gene_name = mysqli_fetch_row($rs)[0]; print "<br>";
                
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
           
    foreach ($Species as $s){
 
        if ($t=="GO terms"){     
                       
            $specie_printed = 0;
            
            foreach (array('C','F','P') as $type){
                
                if ($type=="C"){ $name = "Component";}
                if ($type=="F"){ $name = "Molecular Function";}
                if ($type=="P"){ $name = "Biological Process";}
                
                if (!empty($array[$s]["GO terms"][$type]) and (count($array[$s]["GO terms"][$type])>1 or  $array[$s]["GO terms"][$type]!="-")){
                    
                    if ($specie_printed===0){
                        print "<br><h4>".$s."</h4><br>";
                        $specie_printed=1;
                    }
                    
                    print "<h5>This gene has the following ".$name." GO terms:</h5><br>";
                    // print the GO term names
                    foreach($array[$s]["GO terms"][$type] as $term){
                        if ($term != "-"){
                            print "<p>".$term."</p>";
                        }
                    }  print "<br>";                                   
                
                    if (!empty($array[$s]["GO similar genes"][$type]) and (count($array[$s]["GO similar genes"][$type])>1 or  $array[$s]["GO similar genes"][$type]!="-")){                
                        // print the similar genes:
                        print "<h5>Genes with a similar ".$name." in this specie are...</h5><br>";
                        foreach($array[$s]["GO similar genes"][$type] as $sim_gene){

                            $gene_rec_name = explode(";",$sim_gene)[0];
                            if ($gene_rec_name != "-"){
                            ?>                    
                                <input type="checkbox" class="checkbox" value="" id="final_pubmed" name="pubmed_query[<?php print $gene_rec_name ?>]">                                       
                                <?php print $gene_rec_name; //only if not "-" ?>                    
                            <?php
                            }
                        }  
                    }                               
                }                                
            }           
        }       
        else{          
            
            // print specie header:
            
            if (!empty($array[$s][$t]) and (count($array[$s][$t])>1 or $array[$s][$t][0]!="-")){     
                print "<br><h4>".$s."</h4><br>";
            }
            
            foreach ($array[$s][$t] as $final){ ?>

                <?php if ($final == "-"){  continue; } ?>
                <input type="checkbox" class="checkbox" value="" id="final_pubmed" name="pubmed_query[<?php print $final ?>]">                                       
                <?php print $final; //only if not "-" ?>  

            <?php    
            }
        }
    }
    ?>

<?php

}?>
                   
    <button type="submit" class="btn btn-primary">Submit</button>
</form>    

<script>
var select_all = document.getElementById("select_all"); //select all checkbox
var checkboxes = document.getElementsByClassName("checkbox"); //checkbox items


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

