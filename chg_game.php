<?php
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spiel &auml;ndern"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<div align="center">   
<?php
/*
** Hole alle Spiele
*/
$db_get_games = "SELECT s.SPIEL_ID, s.Kategorie, s.Datum, s.Anpfiff, k.Kategoriename ,t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2, kategorie k
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie=k.KATEGORIE_ID ORDER BY s.Datum ASC, s.Anpfiff ASC";
$result = mysql_db_query($dbName, $db_get_games, $connect);

print('
<table width="80%"  border="0">
  <tr>
    <td><strong>Team1</strong></td>
    <td><strong>Team2</strong></td>
    <td><strong>Kategorie</strong></td>
    <td><strong>Datum</strong></td>
    <td><strong>Anpfiff</strong></td>
  </tr>
');

while ($ausgabe = mysql_fetch_array($result))
{
	print("
	  <tr>
	    <td> 	<a href=\"chg_game2.php?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Name]		</a></td>
	    <td> 	<a href=\"chg_game2.php?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Name2]		</a></td>
	    <td> 	<a href=\"chg_game2.php?gid=$ausgabe[SPIEL_ID]\">  	$ausgabe[Kategoriename]	</a></td>
	    <td>	<a href=\"chg_game2.php?gid=$ausgabe[SPIEL_ID]\">	$ausgabe[Datum]		</a></td>
	    <td>	<a href=\"chg_game2.php?gid=$ausgabe[SPIEL_ID]\">	$ausgabe[Anpfiff]	</a></td>
	  </tr>
	");
}
print("</table>");

?>
</div>
</body>
</html>
