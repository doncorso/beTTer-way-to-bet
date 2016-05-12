<?php
/*
** Hier koennen Daten zu jedem bereits eingetragenem SPiel
** vom ADMIN geaendert werden
*/
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

<?php
print("<form action=\"chg_game2.php\" method=\"post\">");

$game_to_change_ID = $_GET[gid];

  
/*********************************
*** SUBMIT wurde nicht gedrueckt
*********************************/
if(!isset($_POST['submit'])) {     

/*****************************
** Hole zu aenderndes Spiel	**
*****************************/
$db_get_game = "SELECT s.Datum, s.Anpfiff, s.Kategorie, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.SPIEL_ID=$game_to_change_ID";
$game_to_change = mysql_db_query($dbName, $db_get_game, $connect);
$ausgabe_chg_game = mysql_fetch_array($game_to_change);




/*
** Formular
*/
?>
<table width="40%"  border="1" align="center">
  <tr>
    <th width="20%" scope="row">Team 1</th>
    <td width="80%">
        <div align="left">
            <select name="Team1" id="Team1">
	          <?php
	/*
	** Hole alle TEAMS
	*/
	$db_get_teams = "SELECT * FROM team";
	$result_teams = mysql_db_query($dbName, $db_get_teams, $connect);
		while($team_ausgabe = mysql_fetch_array($result_teams))
		{
			print("
		          <option value=\"$team_ausgabe[TEAM_ID]\"");
					if($team_ausgabe[TEAM_ID] == $ausgabe_chg_game[TEAM_ID1])
						print(" selected");
				  print(">$team_ausgabe[Name]</option>");
		}
	?>
            </select>
        </div></td></tr>
  <tr>
    <th scope="row">Team 2 </th>
    <td>  
	    <div align="left">
	      <select name="Team2" id="Team2">
            <?php
	/*
	** Hole alle TEAMS
	*/
	$db_get_teams = "SELECT * FROM team";
	$result_teams = mysql_db_query($dbName, $db_get_teams, $connect);
		while($team_ausgabe = mysql_fetch_array($result_teams))
		{
			print("
		          <option value=\"$team_ausgabe[TEAM_ID]\"");
					if($team_ausgabe[TEAM_ID] == $ausgabe_chg_game[TEAM_ID2])
						print(" selected");
				  print(">$team_ausgabe[Name]</option>");
		}
	?>
          </select>
	    
	    </div></td>
  </tr>

  <tr>
    <th scope="row">Kategorie </th>
    <td>  
	    <div align="left">
	      <select name="Kategorie" id="Kategorie">
            <?php
	/*
	** Hole alle KATEGORIEN
	*/
	$db_get_katos = "SELECT * FROM kategorie";
	$result_katos = mysql_db_query($dbName, $db_get_katos, $connect);
		while($katos_ausgabe = mysql_fetch_array($result_katos))
		{
			print("
		          <option value=\"$katos_ausgabe[KATEGORIE_ID]\"");
					if($katos_ausgabe[KATEGORIE_ID] == $ausgabe_chg_game[Kategorie])
						print(" selected");
				  print(">$katos_ausgabe[Kategoriename]</option>");
		}
	?>
          </select>
	    
	    </div></td>
  </tr>




  <tr>
    <th scope="row">Datum</th>
    <td>
        <div align="left">
          <input name="Datum" type="text" id="Datum" value="<?php print("$ausgabe_chg_game[Datum]"); ?>" size="30%">
        </div></td>
  </tr>
  <tr>
    <th scope="row">Anpfiff</th>
    <td>
        <div align="left">
          <input name="Anpfiff" type="text" id="Anpfiff" value="<?php print("$ausgabe_chg_game[Anpfiff]"); ?>" size="30%">
        </div></td>
  </tr>
</table>


<?php
/*************************************************
** Wenn bereits Ergebnis eingegeben 		**
** zeige Link zum Ruecksetzen des Ergebnisses	**
**************************************************/
if ($ausgabe_chg_game[Tore1]!=-1 || $ausgabe_chg_game[Tore2] != -1)
	{
		print(" <p align=\"center\"> <br>
			<input type=\"checkbox\" name=\"clear_game_result\" value=\"YES\">Ergebnis zurücksetzen
			<br> </p>
		      ");
	}// ********* End Of WENN ERGEBNIS EINGETRAGEN ZEIGE LINK ZUM RUECKSETZEN ***********
?>





<?php

print ("<input name=\"gid\" type=\"hidden\" value=\"$game_to_change_ID\"> ")
?>
    
  <p align="center"><input type="submit" name="submit" value=" Spiel ändern! "></p>
</form>
<?php


} // End OF IF !isset


/*****************
***** SUBMIT *****
*****************/
else
{
	$Team1   = $_POST[Team1];
	$Team2   = $_POST[Team2];
	$Kategorie 	 = $_POST[Kategorie];
	$Datum 	 = $_POST[Datum];
	$Anpfiff = $_POST[Anpfiff];
	$gid	 = $_POST[gid];
	$update_game = "UPDATE spiel SET Team1='$Team1', Team2='$Team2', Kategorie='$Kategorie', Datum='$Datum', Anpfiff='$Anpfiff' 
					WHERE SPIEL_ID='$gid'";	
	
	/*********************************************
	** Wenn Ergebnis zurueckgesetzt werden soll **
	*********************************************/
	if ($_POST[clear_game_result] == "YES")
	{
		$update_game = "UPDATE spiel SET Tore1=-1, Tore2=-1, Team1='$Team1', Team2='$Team2', Kategorie='$Kategorie', Datum='$Datum', Anpfiff='$Anpfiff' 
		WHERE SPIEL_ID='$gid'";	
	}

	if(mysql_query($update_game))
	{
		print("<br>Spiel wurde erfolgreich geändert<br>");
	}
	else
	{
		print("Fehler beim &Auml;ndern des Spiels: <br>$update_game");
	}

	//Wenn Spielergebnis zurückgesetzt werden soll, auch alle Punkte, die auf Tipps vergeben wurden, korrigieren
	if ($_POST[clear_game_result] == "YES")
	{
		$Tipp_Points_Update = "UPDATE tipp SET TippPunkte=0, SpielPunkte=0 WHERE SPIEL_ID=$gid";
		if(mysql_query($Tipp_Points_Update))
		{
			print("<br>Bereits vergebene Punkte wurden wieder auf NULL gesetzt<br>");
		}
		else
		{
			print("Tipppunkte f&uuml;r dieses Spiel konnten nicht auf NULL gesetzt werden.<br> 
			Sobald aber das tats&auml;chliche Ergebnis eingetragen wird, ist alles wieder korrekt!<br>$Tipp_Points_Update");
		}

		//Durchlaufe alle USER ausser admin und berechne die TOTALPOINTS neu
		$sql = "SELECT USER_ID, user FROM user WHERE user != \"admin\" && user != \"Gast\"";
		$result = mysql_query($sql);
		while($users = mysql_fetch_array($result))
		{
			$calc_sum=("SELECT SUM(SpielPunkte) as total FROM tipp WHERE USER_ID=$users[USER_ID]");
			$calc_pointsum=mysql_query($calc_sum);
			$data = mysql_fetch_array($calc_pointsum);
			$totalpoints = $data[total];
			if ($totalpoints == "NULL" || $totalpoints == "")
				$totalpoints = 0;
//			print("<br>calcsum: $calc_sum<br><hr><br>");
			$Total_Points_Update = "UPDATE user SET TotalPoints=$totalpoints WHERE USER_ID=$users[USER_ID]";
			if (mysql_query($Total_Points_Update))
			{
				print("<br>Die Punkte von $users[user] f&uuml;r das Ranking wurden erfolgreich aktualisiert!");
			}
			else
			{
				print("<br>Die Punkte f&uuml;r das Ranking konnten NICHT aktualisiert werden!!!!<br>$Total_Points_Update");
			}
		}
	}


}
?>
</body>
</html>
