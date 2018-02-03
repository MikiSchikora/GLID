<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//load global vars and includes
include "globals.inc.php";

//Store input data in $_SESSION to reaload initial form if necessary

// My query is this:
//print $_REQUEST['myQuery'];

$_SESSION['queryData'] =$_REQUEST;

        // How to do something after a checkbox is marked
      
        if(isset($_REQUEST['Pfam'])){
            $sql_pfamdom = "select pf.name_pfam from Pfam pf, Proteins p WHERE "
                    . "pf.id_pfam = p.id_pfam AND "
                    . "(p.id_Uniprot = '".$_REQUEST['myQuery']."' OR "
                    . "p.prot_recommended_name = '".$_REQUEST['myQuery']."');"; 
            
//            $sql_pfam_similar_prots = "select p.prot_recommended_name from Proteins p, Pfam pf WHERE "
//                    . "p.id_pfam = pf.id_pfam AND "
//                    . "p.id_Uniprot = '".$_REQUEST['myQuery']."'";
//                    
//mysql> select id_Uniprot from Proteins p where id_pfam in ( select id_pfam from Proteins group by id_pfam having p.id_Uniprot = "140U_DROME");            

                                
            $rs = mysqli_query($mysqli, $sql_pfamdom) or print mysqli_error($mysqli);
            
            if (!mysqli_num_rows($rs)){
                print errorPage('Not found','The requested name is not found');
                
            } else {
                $data = mysqli_fetch_assoc($rs);
                
                print headerDBW($_REQUEST['myQuery']);
//                print $data["name_pfam"];
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
            <li><a href="index.php">Home</a></li>
            <li><a href="help.html">Help</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container">

      <h1 class="text-center">GLID: Gene to Literature Integrative Database</h1>
      
      <h4>You searched for: <?php print $_REQUEST['myQuery']?>.</h4>
      <h4>The Pfam domains are <?php print $data["name_pfam"]?></h4>

               
            

<?php
print footerDBW();


            }
        }
?>
