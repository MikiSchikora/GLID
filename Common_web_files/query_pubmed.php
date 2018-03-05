<?php

// THIS CODE IS FOR DISPLAYING A TABLE WITH ALL THE PUBMED RESULTS

ini_set('display_errors', 1);
error_reporting(E_ALL);

////load global vars and includes
include "globals.inc.php";

// Generate the query:
$Names = $_SESSION['queryPubmed'];
if (!empty($_REQUEST['Keywords'])){
    $Keywords = $_REQUEST['Keywords'];
    $query = $Keywords." AND (\"".implode("\" OR \"",$Names)."\")";
}
else{
    $query = "(\"".implode("\" OR \"",$Names)."\")";
}
$final_query = str_replace(" ","+",$query);
$url = "https://www.ncbi.nlm.nih.gov/pubmed/?term=".$final_query."&report=MEDLINE&format=text&dispmax=100";

// load the content of the pubmed result into an array of arrays:
$pubmed_content_array = explode("\n",file_get_contents($url));
$pm_content = array(); // this will contain, for each pubmedID, an array (date of publication, Title, First Author)

$reading_title = 0;
$title_added = 0;
$author_found = 0;
// parse:
foreach($pubmed_content_array as $line){
    
    //PMID
    if (substr($line,0,4)==="PMID"){
        $aut = "";
        $tit = "";
        $title_added = 0;
        $author_found = 0;
        $id = trim(explode("-",$line)[1]);
        $pm_content[$id] = array();
    }
    // DP
    if (substr($line,0,2)==="DP"){
        $dp = trim(explode("-",$line)[1]);
        $pm_content[$id][] = explode(" ",$dp)[0];
    }    

    // Title
    if (substr($line,0,2)=="TI"){
        $reading_title = 1;
        $tit = substr($line,6,100);
    }  
    if ((substr($line,0,2)=="PG" | substr($line,0,3)=="LID") & $title_added==0){
        $reading_title = 0;
        $title_added = 1;
        #$pm_content[$id][] = substr($tit,0,20)."...";  
        $pm_content[$id][] = $tit; 
    }
    if ($reading_title===1 & substr($line,0,2)!="TI"){
        $tit = $tit.trim($line);
    }
    
    // First author
    if (substr($line,0,3)==="FAU" & $author_found===0){
        $aut_content = explode("-",$line);
        if (count($aut_content)>2){
            $aut = trim($aut_content[1])."-".trim($aut_content[2]);
        }
        else{
            $aut = trim($aut_content[1]);
        }
        $pm_content[$id][] = $aut;
        $author_found = 1;
    }
    
}

print headerDBW("PUBMED RESULTS");

?>

<br>
<h4> These are the first 100 papers that match your search in PUBMED:</h4>
<br>

<div class="container-fluid">
                        <table class= "table table-hover" id="pubmedTable">
                            <thead>
                              <tr>
                                <th>PMID</th>
                                <th>Date</th>
                                <th>Title</th>
                                <th>First author</th>
                              </tr>
                            </thead>
                            <tbody>                            
<?php

// body
$t_body = "";
foreach($pm_content as $pmid => $content){   
    
    $url = "https://www.ncbi.nlm.nih.gov/pubmed/".$pmid;
            
    $t_body = $t_body."<tr><td>"."<a href=\"".$url."\" target=\"_blank\">".$pmid."</a>"."</td>";
    $t_body = $t_body."<td>".$content[0]."</td>";
    $t_body = $t_body."<td>".$content[1]."</td>";
    $t_body = $t_body."<td>".$content[2]."</td>"; 
    $t_body = $t_body."</tr>";    
}
print $t_body;

// foot
?> </tbody> </table> </div> 

<script type="text/javascript">
    $(document).ready(function () {
        $('#pubmedTable').DataTable();
    });
</script>

<!-- Add the link to the pubmed-->
<br><br>
<h4> You can also 
    <?php echo '<a href="https://www.ncbi.nlm.nih.gov/pubmed/?term=', urlencode($query), '" target=\"_blank\">Query Pubmed</a> ' ?>
directly.</h4>

<br><br>

<?php print footerDBW(); ?>

