<?php 
/*****************************************************************************************************************************
** Hier kann der administrator Einstellungen zum Turnier vornehmen, wie: Punkteverteilung der Tipps, Name des Turniers usw. **
******************************************************************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Turniereinstellungen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php

//Wenn SUBMIT gedrueckt und seite neu geladen wurde
if (isset($_POST['submit']))
{
    /**************************************
	** UPDATE ADMINS DATA FOR TOURNAMENT **
    **************************************/

	$ID			= $_POST[ID];   
	$Turniername 		= $_POST[Turniername];   
	$Meistername		= $_POST[Meistername];   
	$Meister_Tipp_Points 	= $_POST[Meister_Tipp_Points];   
	$Meister_Tipp_Date	= $_POST[Meister_Tipp_Date];   
	$Meister_Tipp_Time	= $_POST[Meister_Tipp_Time];   
	$Anzahl_Faktorpunkte	= $_POST[Anzahl_Faktorpunkte];   
	$Punkte_korrekter_Tipp	= $_POST[Punkte_korrekter_Tipp];   
	$Punkte_korrekte_Tore	= $_POST[Punkte_korrekte_Tore];   
	$Punkte_korrekter_Sieger= $_POST[Punkte_korrekter_Sieger];   
	$Admin_Vorname           = $_POST[Admin_Vorname];
	$Admin_Nachname          = $_POST[Admin_Nachname];
	$Admin_BIC               = $_POST[Admin_BIC];
	$Admin_IBAN              = $_POST[Admin_IBAN];
	$cash_only               = $_POST[cash_only];
	$Einsatz                 = $_POST[Einsatz];

  $Einsatz_arr = preg_split("/[^\d]+/", $Einsatz, -1, PREG_SPLIT_NO_EMPTY);
	$Einsatz = implode(".", $Einsatz_arr);
	dump ($Einsatz_arr);
  echo "Einsatz = $Einsatz<br>";
   /******************************************************************************
   ** Pruefen, ob Anzahl Faktorpunkte mindestens GLEICH Anzahl SPiele ist, sonst:
   ** Anzahl Faktorpunkte GLEICH Anz. SPiele setzen!
   ******************************************************************************/
   //Wieviele Spiele sind jetzt eingetragen:
	$dbanfrage_SP = "SELECT COUNT(*) FROM spiel";
	$result_SP	  = mysql_db_query ($dbName, $dbanfrage_SP, $connect);
	$ausgabe_SP	  = mysql_fetch_array ($result_SP);	
	$Anzahl_Spiele = $ausgabe_SP[0];
	
	if ( $Anzahl_Faktorpunkte < $Anzahl_Spiele)
	{
		$Anzahl_Faktorpunkte = $Anzahl_Spiele;
	}
	$nurBar = ($cash_only)? 1 : 0;
   
    $SettingsUpdate = "UPDATE settings SET 
						Turniername='$Turniername',
						Meistername='$Meistername',
						Meister_Tipp_Points='$Meister_Tipp_Points',
						Meister_Tipp_Date='$Meister_Tipp_Date',
						Meister_Tipp_Time='$Meister_Tipp_Time',
						Anzahl_Faktorpunkte='$Anzahl_Faktorpunkte',
						Punkte_korrekter_Tipp='$Punkte_korrekter_Tipp',
						Punkte_korrekte_Tore='$Punkte_korrekte_Tore',
						Punkte_korrekter_Sieger='$Punkte_korrekter_Sieger',
						Admin_Vorname='$Admin_Vorname',
						Admin_Nachname='$Admin_Nachname',
						Admin_BIC='$Admin_BIC',
						Admin_IBAN='$Admin_IBAN',
						Nur_Bar='$nurBar',
						Einsatz='$Einsatz' 
						WHERE ID='$ID' ";

	$SettingsInsert= "INSERT INTO settings SET 
						Turniername='$Turniername',
						Meistername='$Meistername',
						Meister_Tipp_Points='$Meister_Tipp_Points',
						Meister_Tipp_Date='$Meister_Tipp_Date',
						Meister_Tipp_Time='$Meister_Tipp_Time',
						Anzahl_Faktorpunkte='$Anzahl_Faktorpunkte',
						Punkte_korrekter_Tipp='$Punkte_korrekter_Tipp',
						Punkte_korrekte_Tore='$Punkte_korrekte_Tore',
						Punkte_korrekter_Sieger='$Punkte_korrekter_Sieger',
						Admin_Vorname='$Admin_Vorname',
						Admin_Nachname='$Admin_Nachname',
						Admin_BIC='$Admin_BIC',
						Admin_IBAN='$Admin_IBAN',
						Nur_Bar='$nurBar',
						Einsatz='$Einsatz'
						";

	mysql_query($SettingsUpdate); 
    //Wenn keine Zele betroffen von UPDATE (weil Datensatz nicht vorhanden)
	if (!mysql_affected_rows()==1)
	{
       //dann Insert
         mysql_query ($SettingsInsert) ;
     }
   
   /************************************
   * eingegebene Anzahl Faktorpunkte muessen als neues DEFAULT fuer die Spalte KONTO bei der Tab USER gesetzt werden
   *************************************/
   $update_default_Konto="ALTER TABLE `user` CHANGE `Konto` `Konto` INT( 11 ) DEFAULT '$Anzahl_Faktorpunkte' NOT NULL";
   mysql_query($update_default_Konto);
   
   /*************************************************************************************************
   ** Jetzt noch die neuen Faktorpunkte bei allen bisher angelegten Spielern aendern (USER->KONTO **
   *************************************************************************************************/
   $update_users_Konto="UPDATE user SET Konto ='$Anzahl_Faktorpunkte' WHERE user != 'admin'";
   mysql_query($update_users_Konto);


/*    $db_getUsers_Konto = "SELECT Konto FROM user";
	$result_userKonto = mysql_db_query ($dbName, $db_getUsers_Konto, $connect);
	$ausgabeInfo = mysql_fetch_array($result_userKonto);
	
	while($ausgabe = mysql_fetch_array ($result))
	{
		
	}   
  */
   
   print('<br> <p align="center"> Die Turnier-Einstellungen wurden aktualisiert. </p><br>');



   
   /************************************
	** Flagge zum hochladen gewaehlt ****
	*************************************/
	if(!empty($datei)) 
	{ 
		// Original-Dateiname des Files verwerfen und die ID des neuen Teams als neuen Namen setzen
		$TID='Logo';
		$TID.='.gif';	
		$datei_name=$TID;
		
		print("<br>Neuer Name: $datei_name<br>");
				
		$dateiname = $datei_name; 
	
	
		if($datei_size > $MAX_FILE_SIZE) 
		{ 
			echo "Die Datei ist zu groß, die maximale Dateigr&ouml;sse beträgt $MAX_FILE_SIZE Byte(s)"; 
		} 
		else 
		{ 
			// Slash fuer Unix-Basis
			copy($datei,"flags/$dateiname"); 
			if( file_exists("flags/$dateiname")) 
			{ 
				echo "<br>Die Datei <b>$datei_name</b> wurde mit <b>$datei_size Byte</b> erfolgreich hochgeladen"; 
			} 
			// Wenn Datei nicht in Unix-FileSystem geladen werden konnte
			elseif(! file_exists("flags/$dateiname")) 
			{ 
				// Probiere mit Backslash auf Windows-Basis zu laden
				copy($datei,"flags\\$dateiname"); 
				if( file_exists("flags\\$dateiname")) 
				{ 
					echo "<br>Die Datei <b>$datei_name</b> wurde mit <b>$datei_size Byte</b> erfolgreich hochgeladen"; 
				} 
				elseif(! file_exists("flags\\$dateiname")) 
				{ 
					echo "<br>Fehler beim Datei-Upload<br>"; 
				} 
			}
					


		} 
	}  //End Of if(!empty...
	/*****  END OF   ********************
	** Flagge zum hochladen gewaehlt ****
	*************************************/
     
}	
//Wenn Seite zum ersten mal geladen wurde (kein Submit)
else
{
/*********************************************************
 Seite mit bisherigen Turnier-Einstellungen zeigen
**********************************************************/
	$dbanfrage = "SELECT * FROM settings ";                
	$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
	$ausgabe = mysql_fetch_array ($result);

?>

<form enctype="multipart/form-data" action="admin_settings.php" method="post">
<div align="center">
	Auf dieser Administrator-Seite k&ouml;nnen die Turniereinstellungen schnell angepasst werden.<br>
	<b>Nach Turnierstart sollte hier m&ouml;glichst nichts mehr ge&auml;ndert werden.</b><br>
	<a href="admin_new_logo.php" target="_self">Diese Zeile anklicken, um ein Turnier-Logo hochzuladen, das beispielweise auf der Startseite angezeigt wird.</a><br>
	<br><br>
	<b> Hier die aktuellen Einstellungen: </b> <br><br>

	<table style="border-width:2px; border-style:solid; padding:3px; spacing:3px;">
		<tr>
			<td>  
				Name des Turniers: (Fu&szlig;ball-Weltmeisterschaft, Wimbledon Grand Slam, ...)<br>
				<input name="Turniername" type="text" value="<?php echo $ausgabe[Turniername]; ?>" size="50" maxlength="120">
			</td>
			<td>
				Punkte für einen korrekt abgegebenen Turnier-Sieger-Tipp:<br>
				<input name="Meister_Tipp_Points" type="text" value="<?php echo $ausgabe[Meister_Tipp_Points]; ?>" size="10" maxlength="120">
			</td>
		</tr>
		<tr>
			<td>
				Name des Turniersiegers: (Fu&szlig;ball-Weltmeister 2014, Wimbledon-Sieger 2010, ...)<br>
        <input name="Meistername" type="text" value="<?php echo $ausgabe[Meistername]; ?>" size="50" maxlength="120">
			</td>
			<td>
				Anzahl Faktorpunkte, die jedem User zur Verf&uuml;gung stehen (Tipp: Anzahl Spiele * 2)<br>
				<input name="Anzahl_Faktorpunkte" type="text" value="<?php echo $ausgabe[Anzahl_Faktorpunkte]; ?>" size="10" maxlength="120">
			</td>		
		</tr>
		<tr>
			<td>
				Sp&auml;testes <b> Datum </b> der Meister-Tipp-Abgabe: (YYYY-MM-DD, z.B. 2006-06-30)<br>
				<input name="Meister_Tipp_Date" type="text" value="<?php echo $ausgabe[Meister_Tipp_Date]; ?>" size="15" maxlength="120">
			</td>
			<td>
				Punkte, die ein Spieler für einen korrekten Tipp erh&auml;lt: (Standard: 3)<br>
				<input name="Punkte_korrekter_Tipp" type="text" value="<?php echo $ausgabe[Punkte_korrekter_Tipp]; ?>" size="10" maxlength="120">
			</td>			
		</tr>
		<tr>
			<td>
				Sp&auml;teste <b> Uhrzeit </b>der Meister-Tipp-Abgabe: (HH:MM:SS, z.B. 15:30:00)<br>
				<input name="Meister_Tipp_Time" type="text" value="<?php echo $ausgabe[Meister_Tipp_Time]; ?>" size="15" maxlength="120">
			</td>
			<td>
				Punkte, die ein Spieler für ein korrekten getipptes Torverh&auml;ltnis erh&auml;lt: (Standard: 2)<br>
				<input name="Punkte_korrekte_Tore" type="text" value="<?php echo $ausgabe[Punkte_korrekte_Tore]; ?>" size="10" maxlength="120">
			</td>		
		</tr>
		<tr>
  		<td>
				Wetteinsatz (in Euro) (EE[.CC], z.B. 10 oder 11.50 etc.)<br>
				<input name="Einsatz" type="text" value="<?php echo sprintf("%.2f", $ausgabe[Einsatz]); ?>" size="15" maxlength="5">
			<td>
				Punkte, die ein Spieler für einen korrekt getippten Sieger erh&auml;lt: (Standard: 1)<br>
				<input name="Punkte_korrekter_Sieger" type="text" value="<?php echo	$ausgabe[Punkte_korrekter_Sieger]; ?>" size="10" maxlength="120">
			</td>
		</tr>
		<tr><td colspan="2" align="center"><b>Administrator-Daten (u.a. f&uuml;r Anmelde-Mails)</b></td></tr>
		<tr>
			<td colspan="2" align="left">
				<input type="checkbox" name="cash_only" value="cash_only" <?php if ($ausgabe[Nur_Bar]) print "checked"; ?>> nur Barzahlung erlauben (die unten stehenden Felder sind damit irrelevant)
			</td>
		</tr>
		<tr>
  		<td>
				<b>Vorname</b> des Administrators (und Kontoinhabers)<br>
				<input name="Admin_Vorname" type="text" value="<?php echo $ausgabe[Admin_Vorname]; ?>" size="15" maxlength="120">
			<td>
				Kontonummber bzw. IBAN des Administrator-Kontos<br>
				<input name="Admin_IBAN" type="text" value="<?php echo	$ausgabe[Admin_IBAN]; ?>" size="15" maxlength="120">
			</td>
		</tr>
		<tr>
  		<td>
				<b>Nachname</b> des Administrators (und Kontoinhabers)<br>
				<input name="Admin_Nachname" type="text" value="<?php echo $ausgabe[Admin_Nachname]; ?>" size="15" maxlength="120">
			<td>
				Bankleitzahl bzw. BIC des Administrator-Kontos<br>
				<input name="Admin_BIC" type="text" value="<?php echo	$ausgabe[Admin_BIC]; ?>" size="15" maxlength="120">
			</td>
		</tr>
	</table>
	
	<input name="ID" type="hidden" value="<?php echo $ausgabe[ID]; ?>"><br>
	<input type="submit" name="submit" value="SAVE">
</div>
</form>


<?php	
} // Ende else
?>

</body>
</html>
