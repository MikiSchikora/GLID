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
    
    //$Specie;
    
    $array[$Specie]= $info;

    

      
      
} 

            
$items=  array_unique($items);

?>

<form name="MainForm" id="mainform-id" autocomplete="off" action="pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top">

<?php
foreach ($items as $t) {
    print "<br><h3>".$t."</h3>";
    ?>                   <div class="form-check">
                   <input type="checkbox" id="select_all"/> Select All
                   </div>
            <?php
            
    foreach ($Species as $s){
        print "<br><h4>".$s."</h4><br>";
        foreach ($array[$s][$t] as $final){
            ?>
               <div class="form-check">
                   <input type="checkbox" class="<?php print $t?>" value="<?php print $final?>" id="final_pubmed" name="<?php print $t."_".$s."_".$final ?>">
                   <label class="form-check-label" for="defaultCheck1">
                       <?php 
                      print(" <h5>$final<br></h5> ");
                       ?>
                   </label>
               </div>
           <?php
        }

    }

    // function
    ?>
    <script>
    var select_all = document.getElementById("select_all"); //select all checkbox
    var checkboxes = document.getElementsByClassName("<?php print $t?>"); //checkbox items


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

}?>
<button type="submit" class="btn btn-primary">Pubmed</button>

    <li><input type="checkbox" id="select_all"/> Select All</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 1</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 2</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 3</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 4</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 5</li>
    <li><input class="checkbox" type="checkbox" name="check[]"> This is Item 6</li>

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

