<?php 
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spiel l&ouml;schen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<div align="center">

<?php

//Ermitteln, welches Spiel geloescht werden soll
$game_to_delete_ID=$_GET["gid"];

//Fuer den Button unten auch nochmal die GAME_ID weitergeben !
print(" <form action=\"delete_game.php?gid=$game_to_delete_ID\" method=\"post\">");


//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if (isset($_POST['submit']))
{
    
    /** SPIEL loeschen in DB  **/
    $delete_game = "DELETE FROM spiel WHERE SPIEL_ID=$game_to_delete_ID";
    mysql_query($delete_game);
    
    /** TIPPS zum SPIEL loeschen in DB  **/
    $delete_tipps = "DELETE FROM tipp WHERE SPIEL_ID=$game_to_delete_ID";
    mysql_query($delete_game);


print("<p align=\"center\"> <h3> Das Spiel - $game_to_delete_ID - wurde erfolgreich geloescht!</h3><br><br>");
print("<p align=\"center\"> <h3> Die Tipps zum Spiel - $game_to_delete_ID - wurden erfolgreich geloescht!</h3><br><br>");
}
/** Seite wird zum ersten mal geoeffnet  **/
else
{

$dbanfrage = "SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
							FROM spiel s, team t1, team t2
							WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND SPIEL_ID = $game_to_delete_ID";
                
$result = mysql_db_query ($dbName, $dbanfrage, $connect);


print("Soll das Spiel <p>");
//Solange ein Spiel aus der Datenbank geholt werden kann
while ($ausgabe = mysql_fetch_array ($result))
{
	print("<b>$ausgabe[Name] </b>gegen <b> $ausgabe[Name2] </b> am $ausgabe[Datum] <p>");
}
print("gel&ouml;scht werden ?<br><br>");

print("<input type=\"submit\" name=\"submit\" value=\" Ja! Loeschen! \">");
}
?>



</form>

</div>
</body>
</html>