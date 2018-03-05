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

// Include directory
$incDir = "$baseDir/include";


// Load accessory routines
include "$incDir/bdconn.inc.php";
include "libDBW.inc.php";

// Start session to store queries
session_start();
