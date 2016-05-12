<?php 
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php /* FIXME!!! change create_menu()! */ ?>
<head>
<title>Team löschen</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style type="text/css">
<!--
.Stil_RED {color: #C84646}
-->
</style>
</head>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<p align="center">
	Ist ein Team in einem angelegten Spiel vorhanden, wird es <strong><span class = Stil_RED>ROT</span></strong> dargestellt und kann nicht gel&ouml;scht werden.
</p>
<p align="center">
	L&ouml;schbare, weil in keinem Spiel vorhandene Teams einfach anklicken, um diese zu l&ouml;schen.
</p>
<p align="center">
	<b>Zu l&ouml;schendes Team ausw&auml;hlen:</b>
	<br><br>
</p>
      
<?php

/******************************
TABELLEN SETTINGS
*******************************/
print ('<table align="center" border="10">
			 <colgroup>
			 <col width="150">
			 <col width="150">
			 </colgroup>');
print("<th align=\"center\">ID</th><th>Name</th>");

/*
** Alle TEAMS holen
*/
$dbanfrage_teams = "SELECT *
					FROM team";              
$result_teams = mysql_db_query ($dbName, $dbanfrage_teams, $connect);

//Solange ein Spiel aus der Datenbank geholt werden kann
while ($ausgabe = mysql_fetch_array ($result_teams))
{
	$dbanfrage_SP = "SELECT COUNT(*)
					FROM spiel WHERE Team1=$ausgabe[TEAM_ID] OR Team2=$ausgabe[TEAM_ID] ";
	$result_SP	  = mysql_db_query ($dbName, $dbanfrage_SP, $connect);
	$ausgabe_SP	  = mysql_fetch_array ($result_SP);	
	$Anzahl_Spiele = $ausgabe_SP[0];

	/** Neu ueberlegt: NOCH NICHT BEKANNT kann wennd er admin will geloescht werdem, da dies evtl.
	 **  selbst organisieren will und es vielleicht "KEINE AHNUNG" nennen will oder so ...
	 **	 sonst folgendes wieder einkommentieren:
	 **     //ID 0 ist NOCH NICHT BEKANNT vorbehalten - sollte man nicht loeschen duerfen !!!
	 **	 	// sollte aber auch durch den SELECT schon nicht vorkommen .... - naja  2 > 1 
	 **	   	//   if($ausgabe[TEAM_ID] != 0 )
 	 *******************************************************/  
   		// Wenn das Team in min. 1 Spiel vorkommt
   		if ($Anzahl_Spiele > 0 )
		{
			print ("<tr valign=\"bottom\" align=\"center\">");
			print ("<td><strong><span class=\"Stil_RED\"> $ausgabe[TEAM_ID] </span></strong></td>"); 
			print ("<td><strong><span class=\"Stil_RED\"> $ausgabe[Name] </span></strong> </td>");   
		} // End Of IF
		else
		{
			print ("<tr valign=\"bottom\" align=\"center\">");
			print ("<td> <a href=\"delete_team.php?tid=$ausgabe[TEAM_ID]\">
	               $ausgabe[TEAM_ID]</a> </td>"); 
			print ("<td> <a href=\"delete_team.php?tid=$ausgabe[TEAM_ID]\">
    	           $ausgabe[Name]</a> </td>");   
		}//End Of ELSE
	//	}//End Of IF
}

print("</table>");
               
?>
                      

</body>
</html>