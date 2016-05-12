<?php
/* 
Hier werden die Datenbank-Informationen
-	DB-Host
-	DB-User
-	DB-Pass
-	DBName
abgefragt,diese Infos in die connect.inc.php geschrieben
und einmalig CREATE TABLES ausgefuehrt, um die notwendige DB-Struktur zu erstellen!
! ein admin und ein gast werden angelegt !
  */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>setting up beTTer</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style type="text/css">
<!--
.Stil1 {color: #000066}
.Stil3 {
	font-size: 12pt;
	font-weight: bold;
	color: #000066;
}
-->
</style>
</head>
<body>


<?php

/*********************************
*** SUBMIT wurde nicht gedrueckt
*********************************/
if(!isset($_POST['submit'])) { 
?>
<h3 align="center" class="Stil1">the beTTer Setup
</h3>
<h4 align="center">Bitte die entsprechenden Informationen eingeben,<br>
  damit beTTer Ihre Datenbank erreichen kann.</h4>
<form action="<?php $PHP_SELF?>" method="post">
<table width="25%" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center"> 
  <tr> 
    <td width="30%">Host</td> 
    <td><input type="text" name="Host" size="20" class="input"
        <?php echo ' value=""'; ?>>
      </td> 
  </tr>

  <tr> 
    <td width="30%">User</td> 
    <td><input type="text" name="User" size="20" class="input"
        <?php echo ' value=""'; ?>>
      </td> 
  </tr>

  <tr> 
    <td width="30%">Pass</td> 
    <td><input type="text" name="Pass" size="20" class="input"
        <?php echo ' value=""'; ?>>
      </td> 
  </tr>

  <tr> 
    <td width="30%">Datenbank</td> 
    <td><input type="text" name="DB" size="20" class="input"
        <?php echo ' value=""'; ?>>
      </td> 
  </tr>
 
 <tr> 
    <td align="center" colspan="2"> 
    <input type="submit" name="submit" value="Einstellungen speichern" class="button"> 
    </td> 
  </tr> 
</table>

<p><span class="Stil3">Hinweis:</span><br>
  Ihre Daten werden in der Datei <strong>connect.inc.php</strong> abgelegt und k&ouml;nnen dort von Ihnen jederzeit angepasst werden.<br>
  Beachten Sie in den folgenden Schritten, welche M&ouml;glichkeiten Ihnen die Datei <strong>setup.php</strong> bietet! <br>
  Sie werden nach dem letzten Schritt darauf hingewiesen, die Datei <strong>setup.php</strong> aus Sicherheitsgr&uuml;nden zu l&ouml;schen! </p>

</form> 

<?php 

}

/**************************************************
***** EINSTELLUNGEN SPEICHERN wurde gedrueckt *****
**************************************************/

elseif($_POST['submit']=="Einstellungen speichern")
{
print("<form action=\"$PHP_SELF\" method=\"post\">");

$datei="connect.inc.php";
$text="<?php 
\$dbHost = \"$_POST[Host]\";
\$dbUser = \"$_POST[User]\";
\$dbPass = \"$_POST[Pass]\";
\$dbName = \"$_POST[DB]\";
\$connect = @mysql_connect(\$dbHost, \$dbUser, \$dbPass) or die(\"Konnte keine Verbindung zum Datenbankserver aufbauen!\");
\$selectDB = @mysql_select_db(\$dbName, \$connect) or die(\"Konnte die Datenbank <b>$dbName</b> nicht ausw&auml;hlen!\");
?>";
?>
<?php 
$fp=fopen($datei,"w");
fwrite($fp,$text);
fclose($fp);

print("<br><br><p align=\"center\">Ihre Daten wurden in der Datei <strong>connect.inc.php</strong>
abgelegt und k&ouml;nnen dort von Ihnen jederzeit angepasst werden.<br>

<br><br>");
print('<br><br>
<input type="submit" name="submit" value="Datenbank-Struktur anlegen" class="button">
</p>
');
?>
  <br>
  <br>
  WICHTIG:<br>
  Durch Anklicken des Buttons <b>Datenbank-Struktur anlegen</b> wird versucht, die Datenbank-Struktur mit Hilfe der von Ihnen eingegebenen Daten anzulegen.<br>
  Falls Sie Fehlermeldungen erhalten, die die Verbindung zur Datenbank betreffen, w&auml;hlen Sie zweimal den ZUR&Uuml;CK - Button Ihres Browsers, um die Zugangsdaten anzupassen.</p>
<p>Folgende Struktur wird versucht werden anzulegen: (Sie k&ouml;nnen dies auch von Hand tun): <br>
- eine Datenbank deren Name in der Datei connect.inc.php unter dbName angegeben sein muss</p>
<blockquote>
  <p><br>
    - Tabelle "settings" mit folgenden Spalten:</p>
  <blockquote>
    <p>- ID (auto increment und KEY)<br>
      - Turniername<br>
      - Meister_Tipp_Points<br>
      - Meister_Tipp_Date<br>
    - Meister_Tipp_Time<br> 
    - Anzahl_Faktorpunkte<br>
    - Punkte_korrekter_Tipp<br>
    - Punkte_korrekte_Tore<br>
    - Punkte_korrekter_Sieger<br>
    - Meistername<br>
    - Admin-Daten: Vor- und Nachname, Kontodaten<br>
		- nur Barzahlung oder auch &Uuml;berweisung erlauben<br>
    - Wetteinsatz
    </p>
  </blockquote>
  <p>- Tabelle "spiel" mit folgenden Spalten</p>
  <blockquote>
    <p> - SPIEL_ID (auto increment und KEY)<br>
      - Datum<br>
      - Anpfiff<br>
      - Team1<br>
      - Team2<br>
      - Tore1<br>
    - Tore2<br>
	- Kategorie
	</p>
  </blockquote>
  <p>- Tabelle "team" mit folgenden Spalten:</p>
  <blockquote>
    <p>- TEAM_ID (auto increment und KEY)<br>
    - Name</p>
  </blockquote>
  <p>- Tabelle "tipp" mit folgenden Spalten:</p>
  <blockquote>
    <p> - USER_ID (KEY)
	                - SPIEL_ID (KEY)
	                <br>
      - Tore1<br>
      - Tore2<br>
      - TippPunkte<br>
      - Faktor<br>
    - SpielPunkte</p>
  </blockquote>
  <p>- Tabelle "user" mit folgenden Spalten:  </p>
  <blockquote>
    <p>- USER_ID (auto increment und KEY)<br>
      - user<br>
      - pass<br>
      - EMail<br>
      - TotalPoints<br>
      - Konto<br>
      - MeisterTipp<br>
      - MeisterTippPunkte
      
    </p>
  </blockquote>

  <p>- Tabelle "kategorie" mit folgenden Spalten:  </p>
  <blockquote>
    <p>- KATEGORIE_ID (auto increment und KEY)<br>
      - Kategoriename
     </p>
  </blockquote>

</blockquote>
<p>   
</form>   
  <?php
} //End Of ELSEIF EINSTELLUNGEN SPEICHERN

/*****************************************************
***** Datenbank-Struktur anlegen wurde gedrueckt *****
******************************************************/
elseif ($_POST['submit']=="Datenbank-Struktur anlegen")
{
require "connect.inc.php";
print("<form action=\"$PHP_SELF\" method=\"post\">");

print("<p align =\"center\"><span class=\"Stil3\">Es wird nun versucht, die Datenbankstruktur anzulegen:</span><br></p>");


$table="settings";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
	  ID smallint(6) NOT NULL auto_increment,
	  Turniername text NOT NULL,
	  Meister_Tipp_Points smallint(6) NOT NULL default '0',
	  Meister_Tipp_Date date NOT NULL default '0000-00-00',
	  Meister_Tipp_Time time NOT NULL default '00:00:00',
	  Anzahl_Faktorpunkte mediumint(9) NOT NULL default '0',
	  Punkte_korrekter_Tipp smallint(6) NOT NULL default '0',
	  Punkte_korrekte_Tore smallint(6) NOT NULL default '0',
	  Punkte_korrekter_Sieger smallint(6) NOT NULL default '0',
	  Meistername text NOT NULL,
	  Admin_Vorname text NOT NULL,
	  Admin_Nachname text NOT NULL,
	  Admin_IBAN text,
	  Admin_BIC text,
		Nur_Bar BOOLEAN default '0',
    Einsatz DOUBLE default '10',
	  PRIMARY KEY  (ID)
	) AUTO_INCREMENT=2 ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");

$table="spiel";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
  SPIEL_ID int(11) NOT NULL auto_increment,
  Datum date NOT NULL default '0000-00-00',
  Anpfiff time NOT NULL default '00:00:00',
  Team1 int(11) NOT NULL default '0',
  Team2 int(11) NOT NULL default '0',
  Tore1 mediumint(9) NOT NULL default '-1',
  Tore2 mediumint(9) NOT NULL default '-1',
  Kategorie int(11) NOT NULL default '0',
  PRIMARY KEY  (SPIEL_ID),
  UNIQUE KEY SPIEL_ID (SPIEL_ID)
) AUTO_INCREMENT=38 ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");

$table="team";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
  TEAM_ID int(11) NOT NULL auto_increment,
  Name varchar(25) NOT NULL default '',
  PRIMARY KEY  (TEAM_ID)
) AUTO_INCREMENT=21 ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");

$table="tipp";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
  USER_ID int(11) NOT NULL default '0',
  SPIEL_ID int(11) NOT NULL default '0',
  Tore1 smallint(5) unsigned NOT NULL default '0',
  Tore2 smallint(5) unsigned NOT NULL default '0',
  TippPunkte int(11) NOT NULL default '0',
  Faktor smallint(5) unsigned NOT NULL default '1',
  SpielPunkte int(11) NOT NULL default '0',
  PRIMARY KEY  (USER_ID,SPIEL_ID)
) ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");

$table="user";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
  USER_ID int(11) NOT NULL auto_increment,
  user varchar(25) NOT NULL default '',
  pass varchar(50) NOT NULL default '',
  EMail varchar(50) NOT NULL default '',
  TotalPoints int(11) NOT NULL default '0',
  Konto int(11) NOT NULL default '31',
  MeisterTipp int(11) NOT NULL default '0',
  MeisterTippPunkte tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (USER_ID),
  UNIQUE KEY user (user),
  UNIQUE KEY USER_ID (USER_ID)
) AUTO_INCREMENT=60 ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");
	



$table="kategorie";
	$create="
	CREATE TABLE IF NOT EXISTS $table (
  KATEGORIE_ID int(11) NOT NULL auto_increment,
  Kategoriename text NOT NULL default '',
  PRIMARY KEY  (KATEGORIE_ID)
) AUTO_INCREMENT=1 ;
	";
	//print("<br>create:<br>$create<br>");
	mysql_query($create);
	print("Tabelle <b>$table</b> erfolgreich angelegt<br><br><br><br>");




/*
** Checken, ob es bereits ein Team mit der ID 0 gibt,
*/
$update_query = "UPDATE team SET TEAM_ID=9999 WHERE TEAM_ID=0";
$update = mysql_query($update_query);
//Wenn Update keine Zeile betrifft, weil noch kein TEAM mit ID 0 angelegt:
 if (mysql_affected_rows()==0)
 {
	/* Team "noch unbekannt" anlegen
	** Wird benoertigt, um Finalsbegegnungen vor Turnierbeginn
	** anzulegen, jedoch noch kein definiertes TEAM dazu zu schreiben
	*/
	$query = "INSERT INTO team SET Name = 'noch unbekannt'";
	$insert = mysql_query($query);
 }
 else 
 {
	/*
	** Es gibt bereits ein Team mit der ID 0 und das MUSS dann "noch unbekannt" sein
	*/
	$update_query = "UPDATE team SET TEAM_ID=0 WHERE TEAM_ID=9999";
	$update = mysql_query($update_query);
 }
 	/* TEAM_ID fuer spaetere Abfragen auf 0 setzen !*/
	$update_TEAM_ID = "UPDATE user SET TEAM_ID=0 WHERE Name='noch unbekannt'";
	$update = mysql_query($update_TEAM_ID);


print("Die notwendige Struktur wurde in Ihrer Datenbank angelegt.<br><br><br>");

print('<input type="submit" name="submit" value="Administrator anlegen" class="button">');
print('</form>');
}//End Of ELSEIF DB-Struktur anlegen

elseif ($_POST['submit']=="Administrator anlegen")
{
require "connect.inc.php";
print("<form action=\"$PHP_SELF\" method=\"post\">");

print("<p align =\"center\"><span class=\"Stil3\">Administrator anlegen</span><br></p>");
print("Der Administrator hat als einziger(!) die Rechte, um Spiele und User anzulegen, diese wieder zu l&ouml;schen, Spielergebnisse einzutragen  usw...<br>");
print("Der Login des Administrators ist \"admin\" und ein nun von Ihnen bestimmtes Passwort.<br>
	Zus&auml;tzlich wird eine g&uuml;ltige E-Mail-Adresse ben&ouml;tigt, um einerseits den Usern die M&ouml;glichkeit zu geben, mit Ihnen in Kontakt zu treten,
	und um Ihnen andererseits ein neu generiertes Passwort zuschicken zu k&ouml;nnen, falls Sie es vergessen haben sollten.<br><br>");

print("Passwort f&uuml;r \"admin-Account\": ");
print('<input type="text" name="AdminPass" size="20" class="input" value=""><br><br>');

print("E-Mail-Adresse des Administrators: ");
print('<input type="text" name="AdminMail" size="20" class="input" value=""><br>');

/*BUTTON*/
print('<br><input type="submit" name="submit" value="Administrator speichern" class="button">');

print("<br><br> Hinweis: <br>Diese Daten k&ouml;nnen Sie jederzeit nach erfolgreichem LogIn unter \"Meine Daten\" &auml;ndern!");
print('</form>');
}// End Of ADMIN ANLEGEN

/**********************
*** ADMIN SPEICHERN ***
**********************/
elseif ($_POST['submit']=="Administrator speichern")
{
require "connect.inc.php";
$AdminPass = md5($_POST[AdminPass]);

print("<p align =\"center\"><span class=\"Stil3\">Administrator anlegen</span><br></p>");

/*
** Checken, ob es bereits einen admin gibt,
**  indem versucht wird, seine USER_ID auf 0 zu setzen
*/
$update_query = "UPDATE user SET USER_ID=9999 WHERE user='admin'";
$update = mysql_query($update_query);
//Wenn Update keine Zeile betrifft, weil noch kein admin angelegt:
 if (mysql_affected_rows()==0)
 {
	/* Admin anlegen*/
	$query = "INSERT INTO user SET Konto=0, user = 'admin', pass = '$AdminPass', EMail = '$_POST[AdminMail]'";
	$insert = mysql_query($query);
	$admin = 0;
 }
 else 
 {
 	print("<br> <p align=\"center\">- Der Benutzer \"admin\" ist bereits vorhanden ... <br> - keine &Auml;nderungen am admin-Account vorgenommen -</p><br>");
	$admin = 1;
 }
 	/* USER_ID des admins der Uebersicht wegen auf 0 setzen !*/
	$update_admin_ID = "UPDATE user SET USER_ID=0 WHERE user='admin'";
	$update = mysql_query($update_admin_ID);
	
print('<br><br>Es wird nun &uuml;berpr&uuml;ft, ob bereits ein "Gast-Account" existiert.<br>
		&Uuml;ber den "Gast-Account" hat jeder die M&ouml;glichkeit, mal in Ihre Tipprunde reinzuschauen, ohne eigene Tipps abgeben zu d&uuml;rfen<br>
		Als eingeloggter "Gast" ist es auch m&ouml;glich, sich beim Administrator f&uuml;r einen "echten" User-Account anzumeldn.<br>
		Der Login f&uuml;r den Gast ist:<br>
		Benutzername: Gast<br>
		Passwort: gast
		<br><br><br>');	
/*
** Checken, ob es bereits einen GAST gibt,
*/
$dbanfrage_GAST = "SELECT COUNT(*)
				  FROM user WHERE user='Gast'";
$result_GAST    = mysql_db_query ($dbName, $dbanfrage_GAST, $connect);
$ausgabe_GAST   = mysql_fetch_array ($result_GAST);	
$Anzahl_GAESTE  = $ausgabe_GAST[0];

if ($Anzahl_GAESTE == 0)
 {
	/* Admin anlegen*/
	$GastPass = md5(gast);
	$query = "INSERT INTO user SET Konto=0, user = 'Gast', pass = '$GastPass'";
	$insert = mysql_query($query);
	$gast=0;
 }
 else 
 {
 	print("<br> <p align=\"center\">- Der Benutzer \"Gast\" ist bereits vorhanden ... <br> - keine &Auml;nderungen am Gast-Account vorgenommen -</p><br>");
	$gast=1;
 }

print('<br><br><p align="center"> 
		Datenbankverbindung gesichert<br>
		Datanbankstruktur angelegt<br>');
		if ($admin==0)
		{
			print ("Administrator-Account angelegt<br>");
		}
		else
		{
			print ("Administrator-Account war bereits angelegt<br>");
		}
		if ($gast==0)
		{
			print ("Gast-Account angelegt<br>");
		}
		else
		{
			print ("Gast-Account war bereits angelegt<br>");
		}
		
		
		print("<br><br>");

print('<br> Sie haben in den letzten Schritten gesehen, welche M&ouml;glichkeiten die Datei <b>"setup.php"</b> bietet.<br>
Da diese Datei nicht davor gesch&uuml;tzt ist, von Unbefugten zu denselben Zwecken genutzt zu werden, empfehlen wir <b>dringend</b> diese Datei zu l&ouml;schen oder an einen Ort zu verschieben, wo sie nicht vom Browser aus aufgerufen werden kann!<br>
Wenn Sie diesen Rat nicht befolgen, stellt dies ein erh&ouml;htes Sicherheitsrisiko f&uuml;r Ihre Tipprunde dar!<br><br>');

print("<br> <a href=\"del_setup.php\">Klicken Sie auf diese Zeile, wenn Sie die Datei jetzt l&ouml;schen lassen wollen! [Empfohlen!]</a><br><br>");
} // End Of Admin speichern

?> 
</p>
</body> 
</html> 
