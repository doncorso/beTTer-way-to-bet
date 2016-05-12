<?php
/* Meister-Tipp  */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array(), array("Gast", "admin"));

/*
Anfrage zur Ermittling des MEISTERNAMENS und 
der spaetesten Tippabgabe !
*/
$dbanfrage = "SELECT Meistername, Meister_Tipp_Date, Meister_Tipp_Time
              FROM settings ";                
$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
$ausgabe = mysql_fetch_array ($result);
		
$user = $_SESSION['user'];

/*******************
aktuelle Zeit holen
*******************/
$heute = today();
$time  = now();

/***********************
Spaeteste Tippabgabezeit <-----
***********************/
/*$tippdate = "2004-06-24";
  $tipptime = "20:45:00";
*/
$tippdate = $ausgabe[Meister_Tipp_Date];
$tipptime = $ausgabe[Meister_Tipp_Time];

//zum Anzeigen anderes Format:
$date = explode('-', $tippdate); //Splitten in Array imer bei einem "-"
$ausgabedatum = $date[2].'.'.$date[1].'.'.$date[0]; //Zusammenfuehren mit "." in umgek. reihenf.
//$ausgabedatum = "24.06.2004";
$time = explode(':',$tipptime);
$ausgabezeit  = $time[0].':'.$time[1];

//if (debug()) {
//  print "GLOBALS = "; print "<pre>"; var_dump($GLOBALS); print "</pre>";
//}

?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Mein Turniersieger-Tipp"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="meistertipp.php" method="post">

<?php
//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if ($_POST['submit'] == " Absenden ") 
{
	/*************************
	Meister - Tipp saven
	**************************/
	$tipped = $_POST[theWinner];
	$tippUpdate = "UPDATE user SET MeisterTipp=$tipped WHERE user=\"$user\"";
	mysql_query($tippUpdate);

	if (debug()) {
	  print "tipped = $tipped<br>";
	  print "tippUpdate = $tippUpdate<br>";
	}

	print("<table width=\"50%\" bgcolor=\"#000000\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" align=\"center\">");
	print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">
				 $user, Dein $ausgabe[Meistername]-Tipp wurde gespeichert.<br>Danke f&uuml;rs Tippen!<br>");
	print("Viel Gl&uuml;ck!<br></th>");
	print("</table>");
	print("<br><hr><br><br>");
}
//Wenn Seite zum ersten mal geladen wurde (kein Submit)
else
{
	$Bezeichnung = "$ausgabe[Meistername]-Tipp";
	// MeisterTipp des Users holen
	$getMeisterTipp =
			"SELECT U.user, T.Name, U.MeisterTipp
			FROM team T, user U
			WHERE T.TEAM_ID = U.MeisterTipp AND U.user = \"$user\"";
	$resultMT = mysql_query($getMeisterTipp);
	$ausgabeMT = mysql_fetch_array ($resultMT);
	//Wenn bereits ein MeisterTipp abgegeben wurde
	if ($ausgabeMT[MeisterTipp] > 0)
	{
		print("<h3 align=\"center\"> Dein Tipp: $ausgabeMT[Name] wird $ausgabe[Meistername].
					<br> <img src=\"flags/$ausgabeMT[MeisterTipp].gif\" alt=\"$ausgabeMT[MeisterTipp]\">
					</h3>");
		$Bezeichnung = "&Auml;ndern in";
	}
	
	/*************************************************
	** 	Wenn noch getippt werden darf (Anpfiff)	**
	**************************************************/

	if (($heute < $tippdate) || ($heute == $tippdate && $time < $tipptime))
	{
		$db_getTeams = "SELECT TEAM_ID, Name 
		                FROM team 
		                WHERE Name != 'noch unbekannt'
		                ORDER BY Name";
		$resultT = mysql_query($db_getTeams);
		print ("</SELECT>");
		print ("<p align=\"center\">$Bezeichnung:  <SELECT NAME='theWinner'>");

		while ($ausgabeTeams = mysql_fetch_array ($resultT) )
		{
				//Team-ID 0 ist reserviert fuer NICHT BEKANNT wegen Finalbegegnungen
				if  ($ausgabeTeams[TEAM_ID] != 0)
				{
						print ("<OPTION VALUE='$ausgabeTeams[TEAM_ID]'>$ausgabeTeams[Name]");
				}
		}
		print ("</p> </SELECT>");

		print("<br><br><br><b>Sp&auml;teste Tippabgabe: $ausgabedatum um $ausgabezeit Uhr </b><br><br>");
		print('<input type="submit" name="submit" value=" Absenden ">
					</form>');
	}
	//Es darf nicht mehr getippt werden
	else
	{
		 print ("<div align=\"center\"><a href=\"view_meistertipps.php\"> Wie haben die anderen getippt? </a><br></div>");        
	}
}
?>
</body>
</html>
