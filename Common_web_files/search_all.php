<?php
// THIS CODE IS FOR SEARCHING ALL
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

$array;
$info;
$items=array();
$something_printed=0;

// Loop through species
foreach ($Species as $Specie){
$info=array();
//    $geneRecName = Null;
//    $protRecName = Null;

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


    if (!mysqli_num_rows($rs)) { 
        $info["Gene recommended name"]=$geneRecName;
        $info["Protein recommended name"]=$protRecName;
        $info["Gene synonyms"]=$GeneSynonyms;
        $info["Protein synonyms"]=$ProteinSynonyms;
        //// AQUÍ FALTEN LA RESTA D'ITEMS !!!!!! ORTOLEGS ET AL
        $array[$Specie]= $info;

        continue;
    }
    
    


    $i=0;
    while ($rsF = mysqli_fetch_array($rs)) {
        while ($i===0){
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

    
    //RECCOMENDED NAMES:
    
    if(isset($_REQUEST['RecName'])){  
        
        if ($protRecName or $geneRecName){
            $something_printed = 1;
            //$info['Specie']=$Specie;   
            if ($geneRecName){
                $info["Gene recommended name"]=$geneRecName;
                $items[]="Gene recommended name";
            }
            if ($protRecName){
                $info["Protein recommended name"]=$protRecName;
                $items[]="Protein recommended name";            
            }
        }
    
    }
    
    if(isset($_REQUEST['Synonyms'])){
        
        //print "here go synonyms<br>";
        if ($GeneSynonyms or $ProteinSynonyms){
            $something_printed = 1;
            if ($GeneSynonyms){
                $info["Gene synonyms"]=$GeneSynonyms;
                $items[]="Gene synonyms";
            }
            if ($ProteinSynonyms){
                $info["Protein synonyms"]=$ProteinSynonyms;
                $items[]="Protein synonyms";
            }
        }
        
    }  
    
    
    
    
    
    
    
    
        
    // FROM HERE IT IS NEW
    
     
           
    // If you want to search for Orthologues
    if(isset($_REQUEST["Orthologues"])){
        
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
            print("YOUR QUERY GENE HAS NO ORTHOLOGUE CLUSTER");
            $info["Gene Orthologues"]=$GeneOrthologues; //empty array
        
            
        //If there is one or more ortho cluster for the query gene:
        } else {

            //print_r($cluster_rs);

            
            //Here we have an array with the different orthologue cluster names and search MySQL with this name
            // I DONT KNOW IF THIS WORKS!!!!!!
            // The idea is to loop through the array of ortho clusters
            $i=0;
            while ($cluster_rsF = mysqli_fetch_array($cluster_rs) and $i<10) {
                $i++;

                //print_r ($cluster_rsF);
                // Query MySQL DB now with a ortho cluster every time
                // WE HAVE TO ADD OPTION TO SELECT ALL GENES EXCEPT MINE!
                $ortho_sql = "SELECT g.gene_recommended_name "
                    . "FROM Gene g, Gene_has_OrthologueCluster goc "
                    . "WHERE g.id_ENTREZGENE = goc.Gene_id_ENTREZGENE "
                    . "AND g.id_ENTREZGENE != '$GeneID[0]' AND goc.OrthologueCluster_ortho_cluster = '$cluster_rsF[0]';";      

                $ortho_rs = mysqli_query($mysqli, $ortho_sql) or print mysqli_error($mysqli); 
            
                //If there are no results of the ortho cluster appart from original gene
                if (!mysqli_num_rows($ortho_rs)) { 
                    //print("No orthologues found <br>");
                    $info["Gene Orthologues"]=$GeneOrthologues; //empty array
                } else {

                    while ($ortho_rsF = mysqli_fetch_array($ortho_rs)) {
                        //I save here the info of the orthologue gene
                        //print_r($ortho_rsF['gene_recommended_name']);
                        //print("<br>");
                        $GeneOrthologues[] = $ortho_rsF['gene_recommended_name']; //This is the gene recommended name of the orthologue gene
                        //print($rsF['gene_recommended_name']);
                    } 
                }
            }
        }

        //Save only unique orthologue names (?)
        $GeneOrthologues=  array_unique($GeneOrthologues);
                        //print_r ($GeneOrthologues);

        // If I have found gene orthologues, I now save it into $info to print it later
        //MAYBE THIS COULD BE PUT INSIDE THE PREVIOUS WHILE!!!
        if ($GeneOrthologues){
                $something_printed = 1;
                $info["Gene orthologues"]=$GeneOrthologues;
                $items[]="Gene orthologues";

        }  
    }
    
    // BEFORE THIS IT IS NEW
    
    
    
    
    
    
    
    //$Specie;
    
    $array[$Specie]= $info;

    

      
      
} 

            
$items=  array_unique($items);

?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top">

<!-- <div class="form-check">-->
    <input type="checkbox" id="select_all"/> Select All
<!--</div>-->
    
<?php
foreach ($items as $t) {
    print "<br><h3>".$t."</h3>"; //only if $array[$s][$t] is not empty
           
    foreach ($Species as $s){
        print "<br><h4>".$s."</h4><br>";
        foreach ($array[$s][$t] as $final){
            ?>
               
                   <input type="checkbox" class="checkbox" value="" id="final_pubmed" name="pubmed_query[<?php print $final ?>]">                        
                      <?php 
                      print $final; //only if not "-"
                       ?>                        
               
           <?php
        }

    }

    // function
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

