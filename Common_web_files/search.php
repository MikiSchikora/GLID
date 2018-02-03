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

$sql_gene= "select g.gene_recommended_name from Gene g, GeneSynonyms gsyn where "
        . "g.id_ENTREZGENE = gsyn.id_ENTREZGENE AND "
        . "(gsyn.name_genesynonym = '".$_REQUEST['myQuery']."' OR "
        . "g.gene_recommended_name = '".$_REQUEST['myQuery']."' or "
        . "g.id_ENTREZGENE = '".$_REQUEST['myQuery']."');";

$sql_protein = "select p.prot_recommended_name from Proteins p, ProteinSynonyms psyn, Gene g where "
        . "g.id_ENTREZGENE = p.id_ENTREZGENE AND p.id_Uniprot = psyn.id_Uniprot AND "
        . "(psyn.name_proteinsynonym = '".$_REQUEST['myQuery']."' OR "
        . "p.prot_recommended_name = '".$_REQUEST['myQuery']."' or "
        . "p.id_Uniprot = '".$_REQUEST['myQuery']."');";

//$sql = "SELECT g.gene_recommended_name from Gene g where g.id_ENTREZGENE='".$_REQUEST['myQuery']."'";


$rs = mysqli_query($mysqli, $sql_gene) or print mysqli_error($mysqli);


if (!mysqli_num_rows($rs)){
    print errorPage('Not found','The requested name is not found');
} else {
    $data = mysqli_fetch_assoc($rs);
    
    print headerDBW($_REQUEST['myQuery']);
    


?>

<nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">GLID project</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="help.html">Help</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container">

      <h1 class="text-center">GLID: Gene to Literature Integrative Database</h1>
      
      <h4>You searched: <?php print $_REQUEST['myQuery']?>.</h4>
      <h4>The recommended name is <?php print $data["gene_recommended_name"]?></h4>
      
      
<?php
       
       
        // How to do something after a checkbox is marked
      
        if(isset($_REQUEST['RecName'])){
            print "hola";
        }


print footerDBW();

}
?>