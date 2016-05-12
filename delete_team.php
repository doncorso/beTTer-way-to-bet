<?php 
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));

$submit = $_POST["submit"];
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Team l&ouml;schen"); 
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
$team_to_delete_ID=$_GET["tid"];

//Fuer den Button unten auch nochmal die TEAM_ID weitergeben !
print(" <form action=\"delete_team.php?tid=$team_to_delete_ID\" method=\"post\">");


//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if ($submit == " Ja! Loeschen! ")
{
    
    /** TEAM loeschen in DB  **/
    $delete_team = "DELETE FROM team WHERE TEAM_ID =$team_to_delete_ID";
    mysql_query($delete_team);
    
print("<p align=\"center\"> <h3> Das Team - $team_to_delete_ID - wurde erfolgreich geloescht!</h3><br><br>");
print("<p align=\"center\"> <h3> Bitte l&ouml;sche alle betroffenen Spiele via 'SPIEL L&Ouml;SCHEN'!</h3><br><br>");
}
/** Seite wird zum ersten mal geoeffnet  **/
else
{

$dbanfrage = "SELECT *
							FROM team
							WHERE TEAM_ID = $team_to_delete_ID";
                
$result = mysql_db_query ($dbName, $dbanfrage, $connect);


print("Soll das Team <p>");
//Solange ein Team aus der Datenbank geholt werden kann
while ($ausgabe = mysql_fetch_array ($result))
{
	print("<b>$ausgabe[Name] </b> geloescht werden ?<br><br>");
}

print("<input type=\"submit\" name=\"submit\" value=\" Ja! Loeschen! \">");
}
?>



</form>

</div>
</body>
</html>