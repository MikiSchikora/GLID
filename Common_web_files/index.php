<?php
//<!DOCTYPE html> may be put in the first row
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "globals.inc.php";

if (isset($_REQUEST['new']) or ! isset($_SESSION['queryData'])){
    $_SESSION['queryData'] = [
        'query' => ''
    ];
}

print headerDBW("Home - GLID project");

?>

<div class="container">

    <form name="MainForm" id="mainform-id" autocomplete="off" action="search_all_new_nav.php" method="POST" enctype="multipart/form-data" class="margin-top">

    <div class="form-group">
    <label>Enter your query</label>
    <input type="text" class="form-control" id="query" name="myQuery" value="" required placeholder="Write here a gene/protein name, UniProt ID or ENTREZGENE ID (CDK2, cyclin dependent kinase 2, CDK2_HUMAN, 1017 ...)" > <!-- value="<?php //print $_SESSION['queryData']['query'] ?>" -->
    </div>
        
     <h5>These are some examples you could try:</h5>
    <span><input type="button" value="Gene: A1BG" onClick="document.getElementById('query').value='A1BG'"></span>
    <span><input type="button" value="Protein: Alpha-1B-glycoprotein" onClick="document.getElementById('query').value='Alpha-1B-glycoprotein'"></span>
    <span><input type="button" value="Gene ID: 1" onClick="document.getElementById('query').value='1'"></span>
    <span><input type="button" value="Protein ID: A1BG_HUMAN" onClick="document.getElementById('query').value='A1BG_HUMAN'"></span>   
    
    
    <h2>Looking for one specie or taxonomic group?</h2>

    <script type="text/javascript">
    var species=["Mus musculus","Saccharomyces cerevisiae","Schizosaccharomyces pombe","Pan troglodytes","Bacillus subtilis","Gallus gallus","Xenopus laevis","Nicotiana tabacum","Escherichia coli","Caenorhabditis elegans","Drosophila melanogaster","Danio rerio","Homo sapiens","Bos taurus"];
    var taxonomy=["Bacteria","Invertebrates","Mammals","Plants and Fungi","Primates","Rodents","Vertebrates"];

    </script>

<!--Make sure the form has the autocomplete function switched off:-->

    <div class="autocomplete" style="width:300px;">
        <label for="exampleInputText">Enter name of specie</label> <br>
        <input id="inputspecie" type="text" name="mySpecie" placeholder="Specie">
    </div>

    <p></p>
    <div class="autocomplete" style="width:300px;">
        <label for="exampleInputText">Enter name of taxonomic group</label>
        <input id="inputtaxonomy" type="text" name="myTaxonomy" placeholder="Taxonomic group">
    </div>
    <br>
    <p> <i>We recommend to <b>fill one of these fields</b>. In case they are empty you'll be querying for all the species in the database.</i></p>

    <script>
    function autocomplete(inp, arr) {
      /*the autocomplete function takes two arguments,
      the text field element and an array of possible autocompleted values:*/
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      inp.addEventListener("input", function(e) {
          var a, b, i, val = this.value;
          /*close any already open lists of autocompleted values*/
          closeAllLists();
          if (!val) { return false;}
          currentFocus = -1;
          /*create a DIV element that will contain the items (values):*/
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "autocomplete-list");
          a.setAttribute("class", "autocomplete-items");
          /*append the DIV element as a child of the autocomplete container:*/
          this.parentNode.appendChild(a);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;
                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists();
              });
              a.appendChild(b);
            }
          }
      });
      /*execute a function presses a key on the keyboard:*/
      inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
              /*and simulate a click on the "active" item:*/
              if (x) x[currentFocus].click();
            }
          }
      });
      function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
      }
      function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
          x[i].classList.remove("autocomplete-active");
        }
      }
      function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
          if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }
      /*execute a function when someone clicks in the document:*/
      document.addEventListener("click", function (e) {
          closeAllLists(e.target);
          });
    }

    /*An array containing all the country names in the world:*/
    /*var countries = ["Afghanistan","Albania","Algeria","Andorra","Angola","Anguilla","Antigua & Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia & Herzegovina","Botswana","Brazil","British Virgin Islands","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central Arfrican Republic","Chad","Chile","China","Colombia","Congo","Cook Islands","Costa Rica","Cote D Ivoire","Croatia","Cuba","Curacao","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France","French Polynesia","French West Indies","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guam","Guatemala","Guernsey","Guinea","Guinea Bissau","Guyana","Haiti","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Isle of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Kosovo","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macau","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauro","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","North Korea","Norway","Oman","Pakistan","Palau","Palestine","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russia","Rwanda","Saint Pierre & Miquelon","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","St Kitts & Nevis","St Lucia","St Vincent","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Timor L'Este","Togo","Tonga","Trinidad & Tobago","Tunisia","Turkey","Turkmenistan","Turks & Caicos","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States of America","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Virgin Islands (US)","Yemen","Zambia","Zimbabwe"];*/

    /*var species = ["Homo Sapiens", "Mus Musculus", "Pan Troglodytes"]

    /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
    autocomplete(document.getElementById("inputspecie"), species);
    autocomplete(document.getElementById("inputtaxonomy"), taxonomy);

    </script>
    

    <h2>What do you want to search?</h2>

    <div class="form-check" id="RecName">
    <input class="form-check-input" type="checkbox" value="" id="RecName" name="RecName">
     <label class="form-check-label" for="defaultCheck1">
     Recommended gene/protein name
     </label>
     <div id="popup_recname" style="display: none">
         <p><i>You will obtain the recommended name, if there is one. Recommended names are retrieved from the UniProt consortium and HGNC (HUGO Gene Nomenclature Commitee).</i></p>
    </div>
    </div>
    
    <script>      
    var e = document.getElementById('RecName');
    e.onmouseover = function() {
        document.getElementById('popup_recname').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_recname').style.display = 'none';
    }
    </script>

    <div class="form-check" id ="Synonyms">
    <input class="form-check-input" type="checkbox" value="" id="Synonyms" name="Synonyms">
     <label class="form-check-label" for="defaultCheck1">
     Synonyms
     </label>
    <div id="popup_syn" style="display: none">
         <p><i>Obtain a list of all the synonyms. From that list, you can select as many as you wish and then proceed to download a JSON file with them and/or query PubMed to maximise your literature search results. The list of all synonyms is retrieved from the UniProt and EntrezGene consortiums</i></p>
    </div>
    </div>
    
    <script>      
    var e = document.getElementById('Synonyms');
    e.onmouseover = function() {
        document.getElementById('popup_syn').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_syn').style.display = 'none';
    }
    </script>

    <div class="form-check" id="Orthologues">
    <input class="form-check-input" type="checkbox" value="" id="Orthologues" name="Orthologues">
     <label class="form-check-label" for="defaultCheck1">
     Phylogenetically related genes
     </label>
    <div id="popup_orth" style="display: none">
    <p><i>Retrieve a list of all phylogenetically related genes annotated inside an OrthoDB group. For a better clarity, we provide a list of genes that are phylogenetically related to the query, as well as which are the species that include each of these genes.
          <a href="http://www.orthodb.org/?page=help" target="blank">Go to the OrthoDB  help page</a> for more information about how OrthoDB clusters phylogenetically related genes.
     </i></p>
      
    </div>   
    </div>
    
    <script>      
    var e = document.getElementById('Orthologues');
    e.onmouseover = function() {
        document.getElementById('popup_orth').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_orth').style.display = 'none';
    }
    </script>

    <div class="form-check" id="Pfam">
    <input class="form-check-input" type="checkbox" value="" id="Pfam" name="Pfam">
     <label class="form-check-label" for="defaultCheck1">
     Similar proteins (same PFAM domains)
     </label>
    <div id="popup_Pfam" style="display: none">
         <p><i>Retrieve a list of all the described proteins which have the exact same PFAM domains in a given specie. From that list, you can select as many as you wish and then proceed to download a file with them and/or query PubMed to maximise your literature search results.</i></p>
    </div>
    </div>  
 
    <script>      
    var e = document.getElementById('Pfam');
    e.onmouseover = function() {
        document.getElementById('popup_Pfam').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_Pfam').style.display = 'none';
    }
    </script>   

    <div class="form-check" id="GO">
    <input class="form-check-input" type="checkbox" value="" id="GO" name="GO">
     <label class="form-check-label" for="defaultCheck1">
     Similar function (Gene Ontology)
     </label>
    <div id="popup_GO" style="display: none">
         <p><i>Obtain a list of all the described genes which, for all the different types of Gene Ontology terms (Cellular Component, Molecular Function, Biological Process), have at least a 80% of intersection with the query. From that list, you can select as many as you wish and then proceed to download a file with them and/or query PubMed to maximise your literature search results.</i></p>
    </div>
    </div>
    
    <script>      
    var e = document.getElementById('GO');
    e.onmouseover = function() {
        document.getElementById('popup_GO').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_GO').style.display = 'none';
    }
    </script>    
                
    <p></p>
    <button type="submit" class="btn btn-primary" id ="submit_to_Pubmed">Submit</button>
    
    <button class="btn btn-primary" onclick="window.location.href='index.php?new=1'">New Search</button>
    
    <div id="popup_submit_to_Pubmed" style="display: none">
         <p><i>BE PATIENT AFTER CLICKING THIS!! We need to process your query. </i></p>
    </div> 
    
    <script>      
    var e = document.getElementById('submit_to_Pubmed');
    e.onmouseover = function() {
        document.getElementById('popup_submit_to_Pubmed').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_submit_to_Pubmed').style.display = 'none';
    }
    </script> 
    
     </form>
 
    <form name="MainForm" id="mainform-id" autocomplete="off" action="pubmed.php" method="POST" enctype="multipart/form-data" class="margin-top" target="_blank">
    <h2>Upload your data from previous searches:</h2>
    <div class="form-group">
        Upload json file: <input type="file" id="json" name="json" value="" width="50" style="width:100%"/>
        <div id="popup_json" style="display: none">
            <p><i>If you already used this website before you may have a json file containing the content of previous search. If so, you can upload it here.</i></p>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

    <script>      
    var e = document.getElementById('json');
    e.onmouseover = function() {
        document.getElementById('popup_json').style.display = 'block';
    }
    e.onmouseout = function() {
        document.getElementById('popup_json').style.display = 'none';
    }
    </script>  

</div><!-- /.container -->

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<?php
print footerDBW();
