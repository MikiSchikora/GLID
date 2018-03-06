<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//load global vars and includes
include "globals.inc.php";

//Store input data in $_SESSION to reaload initial form if necessary

// My query is this:
//print $_REQUEST['myQuery'];

$_SESSION['queryData'] =$_REQUEST;

//If I want to redirect the query to another script
//if ($_REQUEST['myQuery']) {
//    header('Location: search.php?myQuery=' . $_REQUEST['myQuery']);
//}


//First make sure that the option RecName has been selected in the form
if(isset($_REQUEST['RecName'])){
    
    
//Start by looking at the Gene recommended name
//Accepted inputs: ENTREZGENE, recommended name or synonym
$sql_gene= "select g.gene_recommended_name from Gene g, GeneSynonyms gsyn where "
        . "g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND "
        . "(gsyn.name_genesynonym = '".$_REQUEST['myQuery']."' OR "
        . "g.gene_recommended_name = '".$_REQUEST['myQuery']."' or "
        . "g.id_ENTREZGENE = '".$_REQUEST['myQuery']."');";


//$sql = "SELECT g.gene_recommended_name from Gene g where g.id_ENTREZGENE='".$_REQUEST['myQuery']."'";


//Query the database
$rs = mysqli_query($mysqli, $sql_gene) or print mysqli_error($mysqli);


//Print the results if found (How to do something after a checkbox is marked)
if (mysqli_num_rows($rs)) {
    $data = mysqli_fetch_assoc($rs);
    print headerDBW($_REQUEST['myQuery']);   
    ?>
    <h4>You searched: <?php print $_REQUEST['myQuery']?></h4>
    <h4>The recommended name is <?php print $data["gene_recommended_name"]?></h4>
            
    <?php
    print footerDBW();

} else {
    //no gene found
    print "no gene found";
    $sql_protein = "select p.prot_recommended_name from Proteins p, ProteinSynonyms psyn, Gene g where "
        . "g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot AND "
        . "(psyn.name_proteinsynonym = '".$_REQUEST['myQuery']."' OR "
        . "p.prot_recommended_name = '".$_REQUEST['myQuery']."' or "
        . "p.id_Uniprot = '".$_REQUEST['myQuery']."');";

    $rs_prot = mysqli_query($mysqli, $sql_protein) or print mysqli_error($mysqli);
    
    //no gene nor protein found
    if (!mysqli_num_rows($rs_prot)){
        print errorPage('Not found','The requested name is not found');
    } else {
        //protein found
        $data = mysqli_fetch_assoc($rs_prot);    
        print headerDBW($_REQUEST['myQuery']);
        ?>
        <h4>You searched: <?php print $_REQUEST['myQuery']?></h4>
        <h4>The recommended name is <?php print $data["prot_recommended_name"]?></h4>
        <?php   
        print footerDBW();
   
    } //protein rec name found
    
} //no gene found


} else {print "Recommended name not selected";}

?>
