<?php

/*
 * bdconn.inc.php
 * DB Connection
 */

$host = "localhost";
$dbname = "glid";
$user = "root";
$password = "vitray007";
($mysqli = mysqli_connect($host, $user, $password)) or die(mysqli_error());
mysqli_select_db($mysqli, $dbname) or die(mysql_error($mysqli));

//
// mysql> create database glid; #create empty database
// mysql> use glid;
// mysql> source glid_Sqldump.sql; #this is a dump file obtained from mysqldump
