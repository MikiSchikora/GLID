<?php

/*
 * bdconn.inc.php
 * DB Connection
 */

$host = "localhost";
$dbname = "glid";
$user = "root";
$password = "Astrolabi04069400";
($mysqli = mysqli_connect($host, $user, $password)) or die(mysqli_error());
mysqli_select_db($mysqli, $dbname) or die(mysql_error($mysqli));
