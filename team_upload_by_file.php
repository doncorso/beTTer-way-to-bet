<?php
/*
** Hier kann der Admin neue Teams aus einer Datei auslesen ... 
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
$head = create_head("Teams hochladen"); 
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
      $fileData[trim($arr[0])] = trim($arr[1]);
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
    print "Das Team \"$team\" existiert bereits und wird hier nicht neu angelegt. ";
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
** Setzt Bildernamen für eine Mannschaft durch  **
** Umbenennen der geg. Datei ($pic) in          **
** $teamID.$suffix.                             **
**                                              **
** ACHTUNG: das Skript verlässt sich darauf,    **
** dass die Dateien bereits im Ordner ./flags    **
** liegen und nur umbenannt werden müssen!      **
*************************************************/
function setPicForTeam($team, $pic) {
  global $dbName, $connect;  
  
  $dbanfrage_new_team = "SELECT TEAM_ID
						 FROM team
	  					 WHERE Name='$team'";
  $result_ID = mysql_db_query ($dbName, $dbanfrage_new_team, $connect);
  $new_ID = mysql_fetch_array ($result_ID);
			
  // Original-Dateiname des Files verwerfen und die ID des neuen Teams als neuen Namen setzen
  $TID=$new_ID[TEAM_ID];
  if (!$TID) {
    print "<b>Das Team \"$team\" ist unbekannt.</b> Konnte das Bild \"$pic\" nicht zuordnen.<br>";
	return false;
	}

  // Pfad von Dateiname trennen und Dateinamenprefix und -suffix extrahieren
  $path = split("[\\\/]", $pic);
  $filename = $path[count($path)-1]; // nur der Dateiname interessiert, der Rest wird weggeworfen
  $fileNameParts = split("\.", $filename); // nur das Suffix interessiert, der Anfang wird auf TID gesetzt
  
  $newName = $TID. ".". $fileNameParts[count($fileNameParts)-1]; // gibt das Dateinamensuffix zurück

  if (!file_exists("flags/$filename")) {
    print "<b>Die Datei \"flags/$filename\" wurde nicht gefunden.</b><br>";
	print "Bitte überprüfen, ob die Dateien im Ordner ./flags bereits vorhanden sind und dass die Zuordnung<br>";
	print "Mannschaft <--> Bild korrekt ist! Oder wurde dem Team \"$team\" vielleicht schon das korrekte Bild zugewiesen?<br>";
	return false;
	}
  
  if (!rename("flags/$filename", "flags/$newName")) {
    print "<b>Datei \"$filename\" konnte nicht in \"$newName\" umbenannt werden!</b><br>";
	print "Zuordnung zu Mannschaft \"$team\" fehlgeschlagen!<br>";
	return false;
  }
  
  print "Datei \"$filename\" wurde umbenannt in \"$newName\"!<br>";
  return true;
}

?>

<?php if(!isset($_POST['submit'])) { ?>
<form enctype="multipart/form-data" action="<?php $PHP_SELF ?>" method="post">
	<table width="600" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<b>Mannschaften und Bilder</b>
		</td>
		</tr>
		<td>
Bitte beachten:<br>
Die hochzuladende Datei muss eine Textdatei sein und<br>
folgendes (sogenanntes CSV-) Format haben:
<br>
<br>
Mannschaft;Bild[;]
<br>
<br>
Semikoli am Ende werden ignoriert.
<br>
<br>
Bitte beachten:<br>
um die Flaggen richtig zuordnen zu k&ouml;nnen, m&uuml;ssen diese bereits
in den Ordner ./flags/ hochgeladen sein. Dies kannst Du mit der Option
"Flaggen hochladen" sicherstellen.
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
		<input type="submit" name="submit" value="Teams hochladen" class="button">
		</td>
		</tr>
	</table>
</form>
<?php

}else{

	$fileData = load_CSV_File();
	if ($fileData !== false) { // File ok
	
	print "<p align=\"center\">";
	foreach($fileData as $key => $value) {
		if (createTeam($key) !== false) {
			setPicForTeam($key, $value); // Ausgaben: siehe setPicForTeam
		}
	}
	print "</p>";   
  }		
} //End of CLICKED 

?>
</body>
</html>