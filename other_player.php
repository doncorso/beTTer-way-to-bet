<?php 
/*****************************************************************************************************************************
** Hier kann der Admin ein GIF als Turnier-Logo hochladen - es wird ueberschriebn, falls schon vorhanden.
** File: "./flags/Logo.gif"
******************************************************************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);

//Ermitteln, wessen Tipps angezeigt werden sollen
$user_to_show_ID=$_GET["uid"];

$db_getUser_info = "SELECT user FROM user WHERE USER_ID='".$user_to_show_ID."'";
$result_userinfo = mysql_db_query ($dbName, $db_getUser_info, $connect);
$ausgabeInfo = mysql_fetch_array($result_userinfo);

$user_to_show_name = $ausgabeInfo[user];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spieler $user_to_show_name"); 
print $head;
?>
<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php
print("<form action=\"other_player.php?uid=$user_to_show_ID\" method=\"post\">");

/*****************************************************************
 Es folgt allerlei Berechnungskram, der spter verwendet wird...
 ******************************************************************/

// Heutiges Datum und Uhrzeit auslesen (fr Abgleich: Spiel(e) in der Zukunft / Vergangenheit)
$heute = today();
$time  = now();

// ID und maximale Restpunkte vom Benutzer auslesen
$db_getUserID = "SELECT u.USER_ID, u.TotalPoints, u.Konto FROM user u WHERE  user=\"$user\"";
$result1 = mysql_db_query ($dbName, $db_getUserID, $connect);
$ausgabeID = mysql_fetch_array ($result1);
$userID = $ausgabeID[0];
$userKonto = $ausgabeID[Konto];

// Filterung initial auf "die letzten 3" stellen
$chosen_kat="203";
	
// Uebergebener Parameter, der Filterung festlegt
// "%" = keine Filterung (zeige alle), ansonsten sind nur Werte > 0 legal
if ($_POST[kat] > 0 || $_POST[kat] == "%") $chosen_kat = $_POST[kat];

// kat_flag ist das eigentliche Kategorie-Flag, das beim Datenbank-Zugriff benutzt wird
$kat_flag = $chosen_kat;
if ($chosen_kat >= 100) { // values >= 100 sind spezielle Optionen wie z.B. "Die nchsten x Spiele", ergo: nimm alle Kategorien
  $kat_flag = "%";
}

if (debug()) {
  print "chosen_kat = $chosen_kat<br>";
	print "kat_flag   = $kat_flag<br>";
}


	/*********************************************************
	** DropDown mit allen verfuegbaren Kategorien zur	**
	** Auswahl der Filterung der Anzeige der Spiele		**
	**********************************************************/
        
	/*************************
	** Baue DropDown-Box auf**
	**************************/ 
?>
	<p align="center">
	Folgende Spiele anzeigen: 
	<select name="kat" id="kat">
<?php
	/*************************
	** Hole alle KATEGORIEN **
	**************************/
	$db_get_katos = "SELECT * FROM kategorie";
	$result_katos = mysql_db_query($dbName, $db_get_katos, $connect);
		
		/*************************
		**  Flle DropDown-Box  **
		**************************/
		// Extra-Optionen (Annahme: wir werden nie mehr als 100 Kategorien haben)
		print("<option value=203");
	 	if($chosen_kat=="203") print(" selected");
   	print(">Die letzten 3</option>");

		print("<option value=204");
	 	if($chosen_kat=="204") print(" selected");
   	print(">Die letzten 4</option>");

		print("<option value=205");
	 	if($chosen_kat=="205") print(" selected");
   	print(">Die letzten 5</option>");

		print("<option value=210");
	 	if($chosen_kat=="210") print(" selected");
   	print(">Die letzten 10</option>");
 
    // Reine Kategorien hinzufgen 
		while($katos_ausgabe = mysql_fetch_array($result_katos))
		{
			print("<option value=\"$katos_ausgabe[KATEGORIE_ID]\"");
   		if($katos_ausgabe[KATEGORIE_ID] == $chosen_kat) print(" selected");
				  print(">$katos_ausgabe[Kategoriename]</option>");
		}
		
		print("<option value=%");
		if($chosen_kat=="%") print(" selected");
 	 	print(">Alle</option>");
?>
  </select>
	&nbsp &nbsp &nbsp

<?php /* Button zur Aktivierung der Filterung */	?>
	<input type="submit" name="filter" value=" GO! ">
	</p>

	<br><hr><br>

<?php

	/*********************************************************
	** 	Datenbankanfrage, die die Spiele holt, die  **
	** 		zur gewaehlten Kategorie gehoeren         **
	**********************************************************/
    
  // Suche die Spiele der passenden Kategorie
    $dbanfrage = "SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2   
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie LIKE '$kat_flag'
		ORDER BY s.Datum ASC, s.Anpfiff ASC";
                
	if ($chosen_kat >= 200) { // = "letzte n Events", muss andere Abfrage ergeben

  // Suche die letzten n Spiele. Logik: $chosen_kat enthlt bereits die Anzahl zu suchender Spiele, man muss nur 200 abziehen.
	// Grund: Da $chosen_kat eine Zahl zurckgibt, die der ausgewhlten Kategorie entspricht, musste hierfr zuvor 200 hinzuaddiert werden,
	// damit die Auswahl eindeutig bleibt.
	$dbanfrage = "  SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie LIKE '%' AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff <= \"$time\"))
		ORDER BY s.Datum DESC, s.Anpfiff DESC
    LIMIT 0,". ($chosen_kat - 200);

	} 
		
    $result = mysql_db_query ($dbName, $dbanfrage, $connect);   
    
    //Solange ein Spiel aus der Datenbank geholt werden kann
    while ($ausgabe = mysql_fetch_array ($result))
    {

        if (($ausgabe[Datum] < $heute) || (($ausgabe[Datum] == $heute) && ($ausgabe[Anpfiff] < $time))) 
        {
            /*****************************************************************************
            (Anpfiff schon vorbei!)
            ******************************************************************************/


            $getTipp = "SELECT t.Tore1, t.Tore2, t.Faktor, t.TippPunkte FROM tipp t WHERE USER_ID=$user_to_show_ID AND SPIEL_ID=$ausgabe[SPIEL_ID]";
            $result3 = mysql_query($getTipp);
            $ausgabeTor = mysql_fetch_array ($result3);
    
       //Wenn ein Tipp abgegeben wurde
			 // Achtung: Anzahl Tore wird als "---" angezeigt, wenn initial (Uebersetzung des Initialwertes 99).
			 //          Dies liegt daran, dass der Benutzer sofort sehen muss, dass sein nicht abgegebener Tipp
			 //          keine Punkte bringen kann. In die DB kann man "-1" leider nicht initial eintragen, ohne
			 //          den unsigned-Typ von "Tore1" und "Tore2" zu aendern.
       if ($ausgabeTor[0] > -1 || $ausgabeTor[1] > -1 ) {
           $tor1 = correctTor($ausgabeTor[0], MIN_TORE, MAX_TORE, "---");
           $tor2 = correctTor($ausgabeTor[1], MIN_TORE, MAX_TORE, "---");
            }
            //Faktor auslesen und hochzaehlen
            $aktuellFaktor = $ausgabeTor[2];
            $SummeFaktor = $SummeFaktor + ($aktuellFaktor -1);

            $SpielPunkte = $ausgabeTor[TippPunkte] * $aktuellFaktor;

            print ("<a href=\"view_gametipps.php?gid=$ausgabe[SPIEL_ID]\"> Wie haben die anderen getippt? </a><br>");
            print(" Tipp von $user_to_show_name:<br>");
            /******************************
            TABELLEN SETTINGS
            *******************************/
            print ('<table border="0">
                   <colgroup>
                   <col width="150">
                   <col width="20">
                   <col width="150">
                   <col width="160">
                   </colgroup>');

     /****************************
       FLAGS ueber dem Land anzeigen
       *****************************/
       print("<tr><td align=\"right\"> <p><img src=\"flags/$ausgabe[TEAM_ID1].gif\" alt=\"$ausgabe[Name]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\">&nbsp&nbsp&nbsp&nbsp</p></td>");
       print("<td></td>");
       print("<td align=\"left\">   <p>&nbsp&nbsp&nbsp&nbsp<img src=\"flags/$ausgabe[TEAM_ID2].gif\" alt=\"$ausgabe[Name2]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\"></p>   </td></tr>");
       
       /************
       Team - Names
       ************/
    print ("<tr><td align=\"right\">$ausgabe[Name] </td><td align=\"center\">:</td><td>$ausgabe[Name2]</td><td>$tor1 : $tor2</td></tr>");

    /******
    NUR WENN ERGEBNIS eingetragen:
    *******/
    
    if (($ausgabe[Tore1] > -1)&&($ausgabe[Tore2] > -1))
    {
        //DB-Anfrage fuer Besten Tipp dieses Spiels zu holen;
        $db_get_bestTipp="SELECT USER_ID, TippPunkte FROM tipp WHERE SPIEL_ID=$ausgabe[SPIEL_ID] ORDER BY TippPunkte DESC";
        $get_bestTipp_result = mysql_query($db_get_bestTipp);
        $ausgabe_get_bestTipp = mysql_fetch_array ($get_bestTipp_result);

        //DB-Anfrage fuer MEISTE PUNKTE dieses Spiels zu holen;
        $db_get_mostPoints="SELECT USER_ID, SpielPunkte FROM tipp WHERE SPIEL_ID=$ausgabe[SPIEL_ID] ORDER BY SpielPunkte DESC";
        $get_mostPoints_result  = mysql_query($db_get_mostPoints);
        $ausgabe_get_mostPoints = mysql_fetch_array ($get_mostPoints_result);

        
        print ("<tr><td></td><td></td><td align=\"right\">Endergebnis: &nbsp&nbsp</td><td>$ausgabe[Tore1] : $ausgabe[Tore2] </td></tr>");
        print ("<tr><td></td><td></td><td align=\"right\">Tipp-Punkte: &nbsp&nbsp</td><td>$ausgabeTor[TippPunkte] </td>");
        if ($ausgabe_get_bestTipp[TippPunkte] == $ausgabeTor[TippPunkte])
        {
            print("<td>  <p><img src=\"pics/best_Tipp.gif\" alt=\"-->BESTER TIPP<--\"></p>  </td>");
        }
        print("</tr>");
        
        print ("<tr><td></td><td></td><td align=\"right\">Faktor: &nbsp&nbsp</td><td>$aktuellFaktor</td></tr>");
        print ("<tr><td></td><td></td><td align=\"right\">Spiel-Punkte: &nbsp&nbsp</td><td>$SpielPunkte </td>");
        if ($ausgabe_get_mostPoints[SpielPunkte] == $SpielPunkte)
        {
            print("<td>  <p><img src=\"pics/most_Points.gif\" alt=\"-->MEISTE PUNKTE<--\"></p>  </td>");
        }
        print("</tr>");
    }
    print("</table>");
    print(" <br><hr>");

        }//End Of IF(Anpfiff schon vorbei)

    }//End Of WHILE(Ausgabe)

?>

</form>
</body>
</html>