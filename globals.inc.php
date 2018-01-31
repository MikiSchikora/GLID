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
// Blast details, change to adapt to local settings
// Blast databases should be created using the appropriate programs.
$blastHome = "$baseDir/../../blast";
$blastDbsDir = "$blastHome/dbs";
$blastExe = "$blastHome/bin/blastall";
$blastDbs = array("SwissProt" => "sprot", "PDB" => "pdb_seqres.txt");
$blastCmdLine = "$blastExe -d $blastDbsDir/" . $blastDbs['PDB'] . " -p blastp  -e 0.001 -v 100 -b 0 ";

// Include directory
$incDir = "$baseDir/include";

// Load accessory routines
include_once "$incDir/bdconn.inc.php";
include_once "$incDir/libDBW.inc.php";

// Load predefined arrays
// Fulltext search fields
$textFields = Array('e.header', 'e.compound', 'a.author', 's.source', 'sq.header');

// Compounds
$rs = mysqli_query($mysqli, "SELECT * from comptype") or print mysql_error();
while ($rsF = mysqli_fetch_array($rs)) {
    $compTypeArray[$rsF['idCompType']] = $rsF['type'];
}

//expTypes
$rs = mysqli_query($mysqli,"SELECT * from expType") or print mysql_error();
while ($rsF = mysqli_fetch_array($rs)) {
    $expTypeArray[$rsF['idExpType']] = $rsF;
}
//expClasses
$rs = mysqli_query($mysqli,"SELECT * from expClasse") or print mysql_error();
while ($rsF = mysqli_fetch_array($rs)) {
    $expClasseArray[$rsF['idExpClasse']] = $rsF['expClasse'];
}

// Recommended gene name
$rs = mysqli_query($mysqli, "SELECT * from Gene") or print mysql_error();
while ($rsF = mysqli_fetch_array($rs)) {
    $GeneArray[$rsF['id_ENSEMBL']] = $rsF['gene_recommended_name'];
}

// Synonyms

// Orthologues

// Similar proteins

// Start session to store queries
session_start();
