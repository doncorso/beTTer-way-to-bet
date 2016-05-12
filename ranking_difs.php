<?php 
/* Ranking (Abstand) */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);

// Zur Vereinfachung
$user = $_SESSION['user'];

// Teilnehmer auslesen
$db_get_players = "SELECT COUNT(*) FROM user WHERE user != 'admin' AND user != 'Gast'";
$get_players_result = mysql_query($db_get_players);
$ausgabe_get_players = mysql_fetch_row ($get_players_result);
$teilnehmer = $ausgabe_get_players[0];

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Aktuelle Rangliste mit Abstandsanzeige"); 
print $head;
?>

<body>
        
<?php
$menu = create_menu();
print $menu;


print '<h4 align="center">Es spielen insgesamt <font color="#BB0000">'. $teilnehmer. '</font> Teilnehmer mit.<br> </h4>';
print '<h4 align="center"> <a href="ranking.php">Hier gehts zur normalen Rangliste zur&uuml;ck</a> </h4> <br> ';

/******************************
TABELLEN SETTINGS
*******************************/
print ('<table align="center" border="10">
        <colgroup>
        <col width="20">
        <col width="150">
        <col width="50">
        </colgroup>');
print("<th align=\"left\">Platz</th><th>Spieler</th><th>Punkte</th>");

$db_get_ranking = "SELECT USER_ID, user, TotalPoints FROM user WHERE user != 'admin' AND user != 'Gast'";
if ($_SESSION['user'] != "admin") { $db_get_ranking .= " AND user NOT LIKE 'test%'"; }
$db_get_ranking .= " ORDER BY TotalPoints DESC, user ASC";
$get_rankings_result = mysql_query($db_get_ranking);


// Platzierung initialisieren
$platz=1;
//Punkte des previous Datensatzes
$savedPoints =- 100;

$firstrun = 1;
while ($ausgabe_get_ranking = mysql_fetch_array ($get_rankings_result))
{   
	$Points = $ausgabe_get_ranking[TotalPoints];
	
	//Der erste hat keine uebergeordnete Punktzahl !
	if ($firstrun == 1)
	{
		$Points_UP = $Points+1;
	}
	//Abstand zum naechst besser platzierten Spieler
	$PointDiff = $Points_UP-$Points -1;
	
		print ("<tr valign=\"bottom\" align=\"center\"><td>");
		//Wenn Punktgleichheit, dann sind User auf DEMSELBEN Platz!!
		if ($ausgabe_get_ranking[TotalPoints] != $savedPoints) {
			 print ("$platz");
		}
		print("</td><td>");
		
		print ("<b>
					 <a href=\"other_player.php?uid=$ausgabe_get_ranking[USER_ID]\">
					 $ausgabe_get_ranking[user]</a></b>
					 </td><td valign=\"bottom\">");
					 
		$Point_Show=0;
		while ($PointDiff > 0)
		{
			//Alle 5 Punkte Vorsprung Zahl ausgeben
			if ($PointDiff % 5 == 0)
			{
					print("<font size=\"-2\" font color=\"#0000FF\"> $PointDiff</font>");
			}
			else
					print(".");
			print("<br>");
			$PointDiff = $PointDiff - 1;
		}
	
		print("$ausgabe_get_ranking[TotalPoints]</td><tr>");
	
		//Platzierung eins weiterzaehlen
		$platz = $platz +1;
		//Die Punkte sichern um next auf prev vergleichen zu koennen
		$savedPoints=$ausgabe_get_ranking[TotalPoints];
	
	//aktuelle Punkte sind die neuen uebergeordneten Punkte
	$Points_UP = $Points;
	//erster Durchlauf ist vorbei
	$firstrun = 0;
}
print("</table>");
        
        
?>
                
</body>
</html>
