<?php

/*
** Hier kann der Admin neue Spiele aus einer Datei auslesen ... 
*  Dateiformat: CSV
*  Mannschaft; Bild
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spiele hochladen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php
/*************************************************
** Lädt die CSV-Datei.							**
*************************************************/
function load_CSV_file() { 
  $fileData = array();
  $datei_name = $_FILES["datei"]["tmp_name"];

  $fh = fopen($datei_name, 'r');
  $lineData = "line";
  while ($lineData) {
    $lineData = fgets($fh);
	if (trim($lineData)) { // ignore empty lines
      $arr = split(";", $lineData);
      $arrData = array("Kategorie" => trim($arr[0]),
	                   "Datum"     => trim($arr[1]),
					   "Uhrzeit"   => trim($arr[2]),
					   "Team_1"    => trim($arr[3]),
					   "Team_2"    => trim($arr[4]));
					   
      if ($arrData["Team_1"] == "?") $arrData["Team_1"] = "noch unbekannt";
      if ($arrData["Team_2"] == "?") $arrData["Team_2"] = "noch unbekannt";
	  					   
      if ($arrData["Kategorie"] && 
          $arrData["Datum"] && 					   
		  $arrData["Uhrzeit"] && 
		  $arrData["Team_1"] && 
		  $arrData["Team_2"]) { 
        array_push($fileData, $arrData);
		}
	  else {
	    print "Fehlerhafte Zeile: \"$lineData\"!<br>";
		}
	  }
	}
  fclose($fh);
  //print "<pre>"; var_dump($fileData); print "</pre>";

  if (!$fileData || !count($fileData)) {
    print "<p align=\"center\">Datei $datei_name konnte nicht eingelesen werden.<br>Illegales format?</p>";
	return false;
	}
  
  print "<p align=\"center\">Die Datei $datei_name wurde erfolgreich eingelesen.</p>";
  return $fileData;
} 

/*************************************************
** Liest den Wert aus der angegebenen Tabelle   **
** aus der Datenbank aus.                       **
*************************************************/
function getDBData($select, $from, $where) {
  global $dbName, $connect;
  $dbanfrage	 = "SELECT $select 
                    FROM   $from 
					WHERE  $where";
  $result		 = mysql_db_query ($dbName, $dbanfrage, $connect);
  $ausgabe	     = mysql_fetch_array ($result);	
  return $ausgabe[$select]; 
}
/*************************************************
** Prüft, ob eine Kategorie bereits existiert.  **
*************************************************/
function checkIfKategorieExists($kategorie) {
  global $dbName, $connect;
  
  $dbanfrage	 = "SELECT COUNT(*) 
                    FROM kategorie 
					WHERE Kategoriename = '$kategorie'";
  $result		 = mysql_db_query ($dbName, $dbanfrage, $connect);
  $ausgabe	     = mysql_fetch_array ($result);	
  $Anzahl_Kategorien = $ausgabe[0]; 
  return ($Anzahl_Kategorien != 0);
  }

/*************************************************
** Fügt eine Kategorie der Datenbank hinzu.     **
*************************************************/
function insertKategorie($kategorie) {
  return @mysql_query("INSERT INTO kategorie SET Kategoriename = '$kategorie'");
}

/*************************************************
** Legt eine Kategorie an , falls noch nicht    **
** vorhanden.                                   **
*************************************************/
function createKategorie($kategorie) {
  $res = checkIfKategorieExists($kategorie);
  if ($res === true) {
    print "Kategorie \"$kategorie\" existiert bereits und wird hier nicht neu angelegt.<br>";
	return true; // kein echter Fehler!
  }

  if (!insertKategorie($kategorie)) {
    print "Beim Anlegen der Kategorie \"$kategorie\" trat leider ein Fehler auf! Bitte noch ein Mal probieren!<br>";
	return false;
	}

  print "Kategorie \"$kategorie\" wurde erfolgreich angelegt.<br>";
  return true;
}


/*************************************************
** Prüft, ob eine Mannschaft bereits existiert. **
*************************************************/
function checkIfTeamExists($team) {
  global $dbName, $connect;
  
  $dbanfrage	 = "SELECT COUNT(*) FROM team WHERE Name='$team'";
  $result		 = mysql_db_query ($dbName, $dbanfrage, $connect);
  $ausgabe	     = mysql_fetch_array ($result);	
  $Anzahl_Teams = $ausgabe[0]; 
  return ($Anzahl_Teams != 0);
  }

/*************************************************
** Fügt eine Mannschaft der Datenbank hinzu.    **
*************************************************/
function insertTeam($team) {
  return @mysql_query("INSERT INTO team SET Name = '$team'");
}


/*************************************************
** Legt eine Mannschaft an , falls noch nicht   **
** vorhanden.                                   **
*************************************************/
function createTeam($team) {
  $res = checkIfTeamExists($team);
  if ($res === true) {
    print "Das Team \"$team\" existiert bereits und wird hier nicht neu angelegt.<br>";
	return true; // kein echter Fehler!
  }

  if (!insertTeam($team)) {
    print "Beim Anlegen des Teams \"$team\" trat leider ein Fehler auf! Bitte noch ein Mal probieren!<br>";
	return false;
	}

  print "Das Team \"$team\" wurde erfolgreich angelegt.<br>";
  return true;
}

/*************************************************
** Prüft, ob ein Spiel bereits existiert.       **
** ACHTUNG:                                     **
**   - $kategorie, $teamOne und $teamTwo müssen **
**     jeweils als ID angegeben sein!           **
**   - $datum muss im Format JJJJ-MM-TT sein!   **
**   - $uhrzeit muss im Format HH:MM:SS sein!   **
*************************************************/
function checkIfSpielExists($kategorie, $datum, $uhrzeit, $teamOne, $teamTwo) {
  global $dbName, $connect;
  
  $dbanfrage	 = "SELECT COUNT(*) 
                    FROM spiel 
					WHERE Kategorie = '$kategorie'
					AND   Datum     = '$datum'
					AND   Anpfiff   = '$uhrzeit'
					AND   Team1     = '$teamOne'
					AND   Team2     = '$teamTwo'";
					
  $result		 = mysql_db_query ($dbName, $dbanfrage, $connect);
  $ausgabe	     = mysql_fetch_array ($result);	
  $Anzahl_Spiele = $ausgabe[0]; 
  return ($Anzahl_Spiele != 0);
  }

/*************************************************
** Fügt ein Spiel der Datenbank hinzu.          **
*************************************************/
function insertSpiel($kategorie, $datum, $uhrzeit, $teamOne, $teamTwo) {
  //print "insertSpiel: {kategorie, datum, uhrzeit, team 1, team 2} = {";
  //print $kategorie. ", ". $datum. ", ". $uhrzeit. ", ". $teamOne. ", ". $teamTwo. "}<br>";
  return @mysql_query("INSERT INTO spiel 
                       SET Datum = '$datum', Anpfiff = '$uhrzeit', Team1 = '$teamOne', Team2 = '$teamTwo', Kategorie='$kategorie'");
}

/*************************************************
** Legt ein Spiel an , falls noch nicht         **
** vorhanden.                                   **
*************************************************/
function createSpiel($data) {
  $kategorie = $data["Kategorie"];
  $datum     = $data["Datum"];
  $uhrzeit   = $data["Uhrzeit"];
  $teamOne   = $data["Team_1"];
  $teamTwo   = $data["Team_2"];
  //print "{kategorie, datum, uhrzeit, team 1, team 2} = {";
  //print $kategorie. ", ". $datum. ", ". $uhrzeit. ", ". $teamOne. ", ". $teamTwo. "}<br>";

  // Datumsformat für Datenbank anpassen: Schreibweise JJJJ-MM-TT
  $arrDatum = split("\.", $datum, 3);
  $datum_db = $arrDatum[2]."-".$arrDatum[1]."-".$arrDatum[0];

  // Uhrzeitformat für Datenbank anpassen: jeweils 00 Sekunden hinzufügen
  $uhrzeit_db = $uhrzeit.":00";

  // Ggf. Kategorie anlegen
  $res = createKategorie($kategorie);
  if ($res === false) return false; // Ausgaben: siehe createKategorie

  // Ggf. Teams anlegen
  $res = createTeam($teamOne);
  if ($res === false) return false; // Ausgaben: siehe createTeam
  $res = createTeam($teamTwo);
  if ($res === false) return false; // Ausgaben: siehe createTeam

  // jetzt entspr. IDs auslesen (müssen jetzt bereits generiert sein)
  $kategorieID = getDBData("KATEGORIE_ID", "kategorie", "Kategoriename = '$kategorie'");
  if (!$kategorieID && $kategorieID !== 0) { // sollte eigtl. nie passieren
    print "Datenbankfehler: Konnte Kategorie \"$kategorie\" nicht finden. Bitte probieren Sie es nochmals.<br>";
	return false;
  }
 
  $teamOneID = getDBData("TEAM_ID", "team", "name = '$teamOne'");
  if (!$teamOneID && $teamOneID !== 0) { // sollte eigtl. nie passieren
    print "Datenbankfehler: Konnte Team \"$teamOne\" nicht finden. Bitte probieren Sie es nochmals.<br>";
	return false;
  }

  $teamTwoID = getDBData("TEAM_ID", "team", "name = '$teamTwo'");
  if (!$teamTwoID && $teamTwoID !== 0) { // sollte eigtl. nie passieren
    print "Datenbankfehler: Konnte Team \"$teamTwo\" nicht finden. Bitte probieren Sie es nochmals.<br>";
	return false;
  }

  // Prüfen, ob das Spiel bereits existiert
  $res = checkIfSpielExists($kategorieID, $datum_db, $uhrzeit_db, $teamOneID, $teamTwoID);
  if ($res === true) {
    print "<b>Das Spiel \"$teamOne\" gegen \"$teamTwo\" am $datum um $uhrzeit (Kategorie: $kategorie) existiert bereits und wird hier nicht neu angelegt.</b><br>";
	return true; // kein echter Fehler!
  }

  if (!insertSpiel($kategorieID, $datum_db, $uhrzeit_db, $teamOneID, $teamTwoID)) {
    print "<b>Beim Anlegen des Spiels \"$teamOne\" gegen \"$teamTwo\" am $datum um $uhrzeit (Kategorie: $kategorie) trat leider ein Fehler auf! Bitte noch ein Mal probieren!</b><br>";
	return false;
	}

  print "<b>Das Spiel \"$teamOne\" gegen \"$teamTwo\" am $datum um $uhrzeit (Kategorie: $kategorie) wurde erfolgreich angelegt.</b><br>";
  return true;
}

?>

<?php if(!isset($_POST['submit'])) { ?>
<form enctype="multipart/form-data" action="<?php $PHP_SELF ?>" method="post">
	<table width="600" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<b>Spiele</b>
		</td>
		</tr>
		<td>
Bitte beachten:<br>
Die hochzuladende Datei muss eine Textdatei sein und<br>
folgendes (sogenanntes CSV-) Format haben:
<br>
<br>
Kategorie;Datum[TT.MM.JJJJ];Uhrzeit[HH:MM];Mannschaft 1;Mannschaft 2[;]
<br>
<br>
Semikoli am Ende werden ignoriert.<br>
Ein Fragezeichen bei der Mannschaft steht für "noch unbekannt".
<br>
<br>
Beispiele: <br>
Vorrunde;18.06.2010;20:30;England;Algerien<br>
Finale;11.07.2010;20:30;?;?<br>
<br>
<br>
		</td>
		</tr>
		</tr>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="5000">
			<input type="file" name="datei">&nbsp;&nbsp;&nbsp;
		</td>
		</tr>
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<input type="submit" name="submit" value="Spiele hochladen" class="button">
		</td>
		</tr>
	</table>
</form>
<?php

}else{

	$fileData = load_CSV_File();
	if ($fileData !== false) { // File ok
	
	print "<p align=\"center\">";
	foreach($fileData as $key => $value) { // key = 0,1,2,... etc. Uninteresant. In $value stehen alle Spielaten.
		createSpiel($value);
	}
	print "</p>";   
	}	
} //End of CLICKED 

?>
</body>
</html>
