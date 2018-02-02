<?php

/*
 * globals.inc.php
 * Global variables and settings
 */
// Base directories
// Automatic, taken from CGI variables.
$baseDir = dirname($_SERVER['SCRIPT_FILENAME']);
#$baseDir = '/home/dbw00/public_html/PDBBrowser';
$baseURL = dirname($_SERVER['SCRIPT_NAME']);

// Temporal dir, create if not exists, however Web server
// may not have the appropriate permission to do so
$tmpDir = "$baseDir/tmp";
if (!file_exists($tmpDir)) {
    mkdir($tmpDir);
}

// Include directory
$incDir = "$baseDir/include";


// Load accessory routines
include_once "$incDir/bdconn.inc.php";
include_once "$incDir/libDBW.inc.php";

// Load predefined arrays
// Fulltext search fields
//$textFields = Array('e.header', 'e.compound', 'a.author', 's.source', 'sq.header');

// Recommended gene name
//$rs = mysqli_query($mysqli, "SELECT * from Gene") or print mysql_error();
//while ($rsF = mysqli_fetch_array($rs)) {
//    $GeneArray[$rsF['id_ENTREZGENE']] = $rsF['gene_recommended_name'];
//}

// Synonyms

// Orthologues

// Similar proteins

// Start session to store queries
session_start();
