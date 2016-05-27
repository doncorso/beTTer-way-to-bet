<?php
/* Spieltipps aller Spieler  */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array("Gast"));

// Zur Vereinfachung
$user = $_SESSION['user'];

$heute = today();
$time  = now();

//Ermitteln, Tipps von welchem Spiel angezeigt werden sollen
$game_to_show_ID=$_GET["gid"];

if (isset($_GET["gid"])) {

	//Teamnamen des gewaehlten Spiels holen
	$db_get_teams = "SELECT Name, TEAM_ID FROM team, spiel WHERE spiel.Team1=team.TEAM_ID AND spiel.SPIEL_ID=$game_to_show_ID";
	$result_teams = mysql_db_query ($dbName, $db_get_teams, $connect);
	$ausgabeteams = mysql_fetch_array($result_teams);
	$team1 = $ausgabeteams[Name];
	$teamID1 = $ausgabeteams[TEAM_ID];
	
	$db_get_teams = "SELECT Name, TEAM_ID FROM team, spiel WHERE spiel.Team2=team.TEAM_ID AND spiel.SPIEL_ID=$game_to_show_ID";
	$result_teams = mysql_db_query ($dbName, $db_get_teams, $connect);
	$ausgabeteams = mysql_fetch_array($result_teams);
	$team2 = $ausgabeteams[Name];
	$teamID2 = $ausgabeteams[TEAM_ID];
	
	//Hole Datum und Anpfiff des gewaehlten Spiels:
	$db_get_gameinfos = "SELECT Datum, Anpfiff FROM spiel WHERE SPIEL_ID=$game_to_show_ID";
	$result_gameinfos = mysql_db_query ($dbName, $db_get_gameinfos, $connect);
	$ausgabegameinfos = mysql_fetch_array($result_gameinfos);
	$Datum = $ausgabegameinfos[Datum];
	$Anpfiff = $ausgabegameinfos[Anpfiff];

} else { // $_GET["gid"] nicht gesetzt

  // Alle (vergangenen) Spiele holen
	$db_get_games = 
  "SELECT s.SPIEL_ID, s.Kategorie, s.Datum, s.Anpfiff, k.Kategoriename ,t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, 
	        t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
	 FROM spiel s, team t1, team t2, kategorie k
	 WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie=k.KATEGORIE_ID AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff < \"$time\"))
	 ORDER BY s.Datum ASC, s.Anpfiff ASC";
	$result_games = mysql_db_query($dbName, $db_get_games, $connect);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php /* FIXME!!! create_head anpassen! */ ?>
<head>
<title>beTTer - Tipps</title>
<link rel="stylesheet" type="text/css" href="style.css">
<!--<link rel="stylesheet" type="text/css" href="tabelle.css">-->
<link rel="stylesheet" type="text/css" href="tabelle2.css">
</head>

<body>

<?php 
$menu = create_menu();
print $menu;

/******************************************************
* Keine ID bislang gesetzt: Spiel auswaehlen
******************************************************/
if (!isset($_GET["gid"])) {

  print "<div align=\"center\">";
  $num_rows = mysql_num_rows($result_games);
  if (!$num_rows) { // bislang noch kein Spiel vorbei
    print "Hier werden die Tipps f&uuml;r vergangene Spiele angezeigt. Bisher sind noch keine Spiele gespielt.<br>";
  } else { // mind. 1 Spiel vorbei
  
    print "W&auml;hle ein Spiel aus:<br><br>";
  	print('
	  <table width="50%"  border="0">
		  <tr>
			  <td><strong>Team1</strong></td>
			  <td><strong>Team2</strong></td>
			  <td><strong>Kategorie</strong></td>
			  <td><strong>Datum</strong></td>
			  <td><strong>Anpfiff</strong></td>
		  </tr>
	  ');
	
  	while ($ausgabe = mysql_fetch_array($result_games))
  	{
	  	print("
		  	<tr>
			  	<td> 	<a href=\"". myself(). "?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Name]		</a></td>
				  <td> 	<a href=\"". myself(). "?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Name2]		</a></td>
				  <td> 	<a href=\"". myself(). "?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Kategoriename]	</a></td>
				  <td>	<a href=\"". myself(). "?gid=$ausgabe[SPIEL_ID]\">	$ausgabe[Datum]		</a></td>
				  <td>	<a href=\"". myself(). "?gid=$ausgabe[SPIEL_ID]\">	$ausgabe[Anpfiff]	</a></td>
			  </tr>
		  ");
	  }
	  print("</table>");
	  print "</div>";
	} // ENDE mind. 1 Spiel vorbei
  die();
}


/*****************************************************************************
**  Ausgabe: Wer gegen wen?
******************************************************************************/
$team1_src = "flags/". $teamID1. ".gif";
$team2_src = "flags/". $teamID2. ".gif";
$versus    = $team1. " gegen ". $team2;
?>

<div align="center">
  <b>Die Tipps zu: <br>
  <table border="0">
  	<tr>
  		<td><img src="<?php echo $team1_src; ?>" alt="<? echo $team1; ?>"></td>
  		<td valign="middle"><?php echo $versus; ?></td>
  		<td><img src="<?php echo $team2_src; ?>" alt="<? echo $team2; ?>"></td>
  	</tr>
  </table>
</div>


<?php
    

if (($Datum < $heute) || (($Datum == $heute) && ($Anpfiff < $time))) 
{
  /*****************************************************************************
  **			(Anpfiff schon vorbei!)					**
  ******************************************************************************/   
  print("<br><br><br>
    
<table class=\"myTable\" align=\"center\" border=\"0\">
  <thead>
		<tr>
			<th>Tipper</th>
			<th align=\"right\">$team1</th>
			<th align=\"center\">:</th>
			<th align=\"left\">$team2</th>
			<th>Spielpunkte</th>
			<th>Faktor</th>
			<th>Tipppunkte</th>	    	    		
		</tr>
  </thead>
  <tbody>
  ");

  //SQL-Befehl zum Holen aller Tipps zum gewaehlten Spiel aus der DB
  $db_get_tipps = "SELECT t.*, u.USER_ID, u.user 
                  FROM `tipp` t, `user` u 
                  WHERE SPIEL_ID=$game_to_show_ID AND t.USER_ID = u.USER_ID 
                  ORDER BY u.user ASC";
  
  $result_tipps = mysql_db_query ($dbName, $db_get_tipps, $connect);
  
  $var = -1;
  //Solange ein Tipp aus der Datenbank geholt werden kann
  while ($ausgabetipps = mysql_fetch_array($result_tipps))
  {
  	$useridoftipp = $ausgabetipps[USER_ID];

    //Username des aktuellen Tipps holen
    $db_get_userinfos = "SELECT user FROM user WHERE USER_ID=$useridoftipp";
    $result_userinfos = mysql_db_query ($dbName, $db_get_userinfos, $connect);
    $ausgabeuserinfos = mysql_fetch_array($result_userinfos);
    $user_of_tipp = $ausgabeuserinfos[user];
		
		$tor1 = correctTor($ausgabetipps[Tore1], MIN_TORE, MAX_TORE, "---");
		$tor2 = correctTor($ausgabetipps[Tore2], MIN_TORE, MAX_TORE, "---");
		
		print ("<tr>");
		$td = ($var > 0)? '<td class="odd"' : '<td';
		print("
		    $td align=\"right\">$user_of_tipp:</td>
				$td align=\"right\">$tor1</td>
				$td align=\"center\">:</td>
				$td align=\"left\">$tor2</td>
				$td align=\"center\">$ausgabetipps[SpielPunkte]</td>
				$td align=\"center\">$ausgabetipps[Faktor]</td>
				$td align=\"center\">$ausgabetipps[TippPunkte]</td>	    	  
			</tr>
		");
			
		$var *= -1;	
  }

  print("</tbody>
	<tfoot>
		<tr>
			<td colspan=\"100%\"> </td>
		</tr>
	</tfoot>
</table>");

//  print("<br><br><br>
//<table align=\"center\" border=\"0\">
//  <thead>
//		<tr>
//			<th class=\"mytable\">Tipper</th>
//			<th align=\"right\">$team1</th>
//			<th align=\"center\">:</th>
//			<th align=\"left\">$team2</th>
//			<th>Spielpunkte</th>
//			<th>Faktor</th>
//			<th>Tipppunkte</th>	    	    		
//		</tr>
//  </thead>
//  <tbody>
//  ");
//
//  //SQL-Befehl zum Holen aller Tipps zum gewaehlten Spiel aus der DB
//  $db_get_tipps = "SELECT * FROM tipp WHERE SPIEL_ID=$game_to_show_ID";
//  $result_tipps = mysql_db_query ($dbName, $db_get_tipps, $connect);
//  
//  $var = -1;
//  //Solange ein Tipp aus der Datenbank geholt werden kann
//  while ($ausgabetipps = mysql_fetch_array($result_tipps))
//  {
//  	$useridoftipp = $ausgabetipps[USER_ID];
//
//    //Username des aktuellen Tipps holen
//    $db_get_userinfos = "SELECT user FROM user WHERE USER_ID=$useridoftipp";
//    $result_userinfos = mysql_db_query ($dbName, $db_get_userinfos, $connect);
//    $ausgabeuserinfos = mysql_fetch_array($result_userinfos);
//    $user_of_tipp = $ausgabeuserinfos[user];
//		
//		print ("<tr>");
//		$td = ($var > 0)? '<td class="odd"' : '<td';
//		print("$td align=\"right\">$user_of_tipp:</td>
//				$td align=\"right\">$ausgabetipps[Tore1]</td>
//				$td align=\"center\">:</td>
//				$td align=\"left\">$ausgabetipps[Tore2]</td>
//				$td align=\"center\">$ausgabetipps[SpielPunkte]</td>
//				$td align=\"center\">$ausgabetipps[Faktor]</td>
//				$td align=\"center\">$ausgabetipps[TippPunkte]</td>	    	  
//			</tr>
//		");
//			
//		$var *= -1;	
//  }
//
//  print("</tbody>
//	<tfoot>
//		<tr>
//			<td colspan=\"100%\"> </td>
//		</tr>
//	</tfoot>
//</table>");


}
else
{
	print("<br><br> Das Spiel $team1 gegen $team2 ist noch nicht angepfiffen.<br>
		     Erst nach Anpfiff k&ouml;nnen Tipps nicht mehr ver&auml;ndert und von anderen Benutzern eingesehen werden<br><br>");
}

?>

</body>
</html>