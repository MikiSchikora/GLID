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

//////////////////
//////////////////
//////////////////
//////////////////
// HEEEEEEEEEEEYYYYYYYY
// YOU HAVE TO EDIT THE action="search_Synonims.php" EACH TIME
//////////////////
//////////////////
//////////////////
//////////////////

print headerDBW("Home - GLID project");

?>


    <div class="container">

      <!--h1 class="text-center">GLID: Gene to Literature Integrative Database</h1-->

      <form name="MainForm" id="mainform-id" autocomplete="off" action="search_RecName_miki.php" method="POST" enctype="multipart/form-data" class="margin-top">
     
      
     <!-- <form name="MainForm" id="mainform-id" autocomplete="off" action="search_Synonims.php" method="POST" enctype="multipart/form-data" class="margin-top">
     -->
          
      <div class="form-group">
        <label>Enter your query</label>
        <input type="text" class="form-control" id="query" name="myQuery" value="" required placeholder="Write here a gene/protein name, UniProt ID or ENTREZGENE ID (CDK2, cyclin dependent kinase 2, CDK2_HUMAN, 8246 ...)" > <!-- value="<?php //print $_SESSION['queryData']['query'] ?>" -->
      </div>


          
      <h2>Looking for one specie or taxonomic group?</h2>

<?php
$result_sp = mysqli_query( $mysqli, "SELECT Species.common_name FROM Species");
$result_tax = mysqli_query( $mysqli, "SELECT Taxonomy.name_taxonomy FROM Taxonomy");
?>
<script type="text/javascript">
var species=new Array();
var taxonomy=new Array();

<?php
$counter = 0;
while($row = mysqli_fetch_array($result_sp))
{
   printf("species[%d]='%s'\n", $counter++, $row[0]);
}
$counter = 0;
while($row = mysqli_fetch_array($result_tax))
{
   printf("taxonomy[%d]='%s'\n", $counter++, $row[0]);
}
?>
</script>


 <!-- This jQuery script allows to submit the form by fillin one out of Species or Taxonomy 
 If none are filled, you cannot submit the form, if one is filled, ok.
 We have added in the php script a message that says you cannot fill both of them -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
jQuery(function ($) {
    var $inputs = $('input[name=mySpecie],input[name=myTaxonomy]');
    $inputs.on('input', function () {
        // Set the required property of the other input to false if this input is not empty.
        $inputs.not(this).prop('required', !$(this).val().length);
    });
});

    </script>


<!--Make sure the form has the autocomplete function switched off:-->
<!--form autocomplete="off" action="/action_page.php"-->
  <div class="autocomplete" style="width:300px;">
    <label for="exampleInputText">Enter name of specie</label>
    <input id="inputspecie" type="text" name="mySpecie" placeholder="Specie">
  </div>

<p></p>
 <div class="autocomplete" style="width:300px;">
    <label for="exampleInputText">Enter name of taxonomic group</label>
    <input id="inputtaxonomy" type="text" name="myTaxonomy" placeholder="Taxonomic group">
  </div>
  <!--input type="submit"-->

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

       <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="RecName" name="RecName">
         <label class="form-check-label" for="defaultCheck1">
         Recommended gene/protein name
         </label>
       </div>

       <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="Synonyms" name="Synonyms">
         <label class="form-check-label" for="defaultCheck1">
         Synonyms
         </label>
       </div>

       <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="Orthologues" name="Orthologues">
         <label class="form-check-label" for="defaultCheck1">
         Orthologues
         </label>
       </div>

       <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="Pfam" name="Pfam">
         <label class="form-check-label" for="defaultCheck1">
         Similar proteins (same PFAM domains)
         </label>
       </div>

       <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="GO" name="GO">
         <label class="form-check-label" for="defaultCheck1">
         Similar function (Gene Ontology)
         </label>
       </div>


          
          
        <p></p>
        <button type="submit" class="btn btn-primary">Submit</button>
        <button class="btn btn-primary" onclick="window.location.href='index.php?new=1'">New Search</button>
      </form>



    </div><!-- /.container -->

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<?php
print footerDBW();
