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
<b>Zu l&ouml;schendes Spiel ausw&auml;hlen:</b><br><br>
<?php


    /******************************
    TABELLEN SETTINGS
    *******************************/
    print ('<table align="center" border="10">
           <colgroup>
           <col width="90">
           <col width="80">
           <col width="150">
           <col width="150">
           </colgroup>');
print("<th align=\"left\">Datum</th><th>Anpfiff</th><th>Team1</th><th>Team2</th>");


$dbanfrage = "	SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID ORDER BY s.Datum ASC, s.Anpfiff ASC";
                
$result = mysql_db_query ($dbName, $dbanfrage, $connect);


//Solange ein Spiel aus der Datenbank geholt werden kann
while ($ausgabe = mysql_fetch_array ($result))
{
   
   /*********************************************************
     Wenn noch kein Ergebnis vom admin eingetragen wurde ...
     waere ja doof, ein Spiel zu loeschen, was schon ein Ergebnis hat...
     **********************************************************/
   if (($ausgabe[Tore1] < 0) && ($ausgabe[Tore2] < 0) )
   {
        print ("<tr valign=\"bottom\" align=\"center\">");
        print ("<td> $ausgabe[Datum] </td>");
        print ("<td> $ausgabe[Anpfiff] </td>");
        print ("<td> <a href=\"delete_game.php?gid=$ausgabe[SPIEL_ID]\">
               $ausgabe[Name]</a> </td>");
        print ("<td> <a href=\"delete_game.php?gid=$ausgabe[SPIEL_ID]\">
               $ausgabe[Name2]</a> </td>");

   }
   }





print("</table>");
        
        
?>
</div>                
</body>
</html>