<?php 
$dbHost = "";
$dbUser = "";
$dbPass = "";
$dbName = "";
$connect = @mysql_connect($dbHost, $dbUser, $dbPass) or die("Konnte keine Verbindung zum Datenbankserver aufbauen!");
$selectDB = @mysql_select_db($dbName, $connect) or die("Konnte die Datenbank <b></b> nicht auswählen!");
?>