<?php
require ("general_methods.inc.php");
require ("connect.inc.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>beTTer testing</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style type="text/css">
<!--
.Stil1 {color: #000066}
.Stil3 {
	font-size: 12pt;
	font-weight: bold;
	color: #000066;
}
-->
</style>
</head>
<body>

<?php
echo "PHP version: ". phpversion(). "<br><br>";
?>

<?php
//$date = date("YmdHis");
//$dateArr = split_timestamp($date);
//echo "date = ". $date. "<br>";
//
//echo "dateArr = "; 
//dump($dateArr);
//
//print "full date = ". timestamp_to_full_date($date). "<br>";
//
//$morgen = add_dates($date, "00000001000000");
//print "morgen        = ". $morgen. "<br>";
//print "morgen (full) = ". timestamp_to_full_date($morgen). "<br>";
//
//$gestern = add_date($date, 0, 0, -1, 0, 0, 0);
//print "gestern       = ". $gestern. "<br>";
//print "gestern(full) = ". timestamp_to_full_date($gestern). "<br>";
?>

<?php
//$Befehl2    = "SELECT `Datum`,`Anpfiff` FROM `spiel` ORDER BY 'Datum','Anpfiff' LIMIT 1";
//$Ergebnis2  = mysql_db_query ($dbName, $Befehl2, $connect);
//$ausgabe2   = mysql_fetch_array ($Ergebnis2);
//$startDatum = $ausgabe2['Datum'];
//$startZeit  = $ausgabe2['Anpfiff'];	
//$start      = date_time_to_full_date($startDatum, $startZeit);
//
//print "startDatum = ". $startDatum. "<br>";
//print "startZeit  = ". $startZeit. "<br>";
//print "start      = ". $start. "<br>";
//
//// Generiere spätesten Anmeldezeitpunkt = Anpfiff-1 Tag
//$fullStartDatum = date_to_timestamp($startDatum. " ". $startZeit);
//print "fullStartDatum = ". $fullStartDatum. "<br>";
//
//// spätester Zeitpunkt zum Registrieren: genau 1 Tag vorher
//$latestReg = add_date($fullStartDatum, 0, 0, -1, 0, 0, 0);
//$fullLatestReg = timestamp_to_full_date($latestReg);
//print "fullLatestReg = ". $fullLatestReg. "<br>";
//
//$vorname = "Ulf";
//
//$text_user = "
//Hallo $vorname,\n
//um Deinen beTTer-Account freischalten zu lassen, musst Du dem Administrator noch<br>
//Deinen Einsatz zukommen lassen, und zwar bis zum %latest_reg%.<br> 
//Turnierbeginn ist am %turnier_start%.<br> 
//";
//
//$text_user = str_replace("%turnier_start%", $start        , $text_user); // ersetzte %turnier_start%
//$text_user = str_replace("%latest_reg%"   , $fullLatestReg, $text_user); // ersetzte %latest_reg%	
//
//print "<br>". $text_user. "<br>";
?>

<?php
//$differenz = 1;
//print ("Bitte vergib ". singplur("einen", $differenz, $differenz) . " ". singplur("Faktorpunkt", "Faktorpunkte", $differenz). " weniger!<br>");
//
//$differenz = 2;
//print ("Bitte vergib ". singplur("einen", $differenz, $differenz) . " ". singplur("Faktorpunkt", "Faktorpunkte", $differenz). " weniger!<br>");
?>


<?php
//$username = "blubb";
//$db_userID = "SELECT USER_ID FROM user WHERE user = '$username'";
//$result_userID = mysql_query($db_userID);
//$row_userID = mysql_fetch_assoc($result_userID);
//$userID = $row_userID[USER_ID];
//print "userID = $userID<br>";
//	
//$db_spiele     = "SELECT * FROM `spiel`";
//$result_spiele = mysql_query($db_spiele);
//
//while ($row = mysql_fetch_assoc($result_spiele)) {
//
//	$spielID = $row[SPIEL_ID];
//
//	// Tipp mit aktuellem Spiel und $userID abgeben
//  $db_add_tipp = "INSERT INTO tipp SET USER_ID='$userID', SPIEL_ID='$spielID', Tore1='99', Tore2='99', TippPunkte='0', Faktor='1', Spielpunkte='0'";
//  $result_add_tipp = mysql_query($db_add_tipp);
//	if (!$result_add_tipp) {
//		print "Konnte Spiel $spielID nicht f&uuml;r user $userID tippen - schon getippt?<br>";
//	}
//}
?>


<?php
$entries = array( array("href" => "mylogin.php"          , "text" => "Meine Daten"),
                  array("href" => "tab.php"              , "text" => "Meine Tipps"),
                  array("href" => "meistertipp.php"      , "text" => "Mein Turniersieger-Tipp"),
                  array("href" => "ranking.php"          , "text" => "Aktuelle Rangliste"),
                  array("href" => "gewinnverteilung.php" , "text" => "Aktuelle Gewinnverteilung"),
                  array("href" => "tipphelp.php"         , "text" => "Hilfe zum Tippen"),
                  array("href" => "logout.php"           , "text" => "Logout"),
                );

$menu = create_menu("Men&uuml;", array(1,2,3));
print "menu 1 = <br>$menu<br>";

$menu = create_menu("Men&uuml;", $entries);
print "menu 2 = <br>$menu<br>";

?>
</body> 
</html> 
