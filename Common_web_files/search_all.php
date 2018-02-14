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
$items;

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

    foreach ($items as $t) {
        print "<br><h3>".$t."</h3><br>";
        foreach ($Species as $s){
            print "<h4>".$s."</h4><br>";
            foreach ($array[$s][$t] as $final){
                print_r ($final);
                print "<br>";
            }
                        
        }
                
                
    }

print "<br>";
print footerDBW();   
    
// Debug if you didn't find anything
if (!$something_printed){
    print(" <h5> Your search gave no results </5>");
}
