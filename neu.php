<?php
/*
Hier kann vom admin ein neuer USER angelegt werden
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));

// -------------------------------------------------------------------------------------------
//	dataOK()
//	Gibt true oder einen Fehler-String zurueck.
//  Diese Methode steht derzeit nicht in den general_methods.inc.php, weil dort sonst 
//  connect.inc.php required waere.
//  
//  Ausserdem spart man sich alle Parameter, weil die hier eh bekannt sind.
//
//  TODO: Ueberlegen, ob es Sinn hat, connect.inc.php in genral_method.inc.php zu includen! 
//        Gibt es noch andere DB-Abfragen, die man dort gerne haette? 
//        Problem: Ohne connect.inc.php koennte man dann auch general_method.inc.php
//        nicht mehr includen!
// -------------------------------------------------------------------------------------------
function dataOK() {

  if ((!$_POST['username'])  || ($_POST['username']  == "") ||
      (!$_POST['password'])  || ($_POST['password']  == "") ||
      (!$_POST['password2']) || ($_POST['password2'] == "") ||
      (!$_POST['email'])     || ($_POST['email']     == "")) {
 	  return "Bitte alle Felder ausf&uuml;llen."; 
 	}
        
  if($_POST['password'] != $_POST['password2']) {
    return "Die Passw&ouml;rter stimmen nicht &uuml;berein.";
  }

  $err = check_login_name($_POST['username'], ILLEGAL_CHARS);
  if ($err !== true) return str_replace("Ung", "Benutzername: ung", $err);
   
  $err = check_login_name($_POST['password'], " "); // only illegal character for password is space
  if ($err !== true) return str_replace("Ung", "Passwort: ung", $err);
  
	// all ok
  return true;
}
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Neuen User anlegen"); 
print $head;
?>

<body>
<div align="center">

<?php
$menu = create_menu();
print $menu;


/***************************
 * Felderinhalte auslesen
 ***************************/
if(!isset($_POST['submit'])) { 

  // Try to init. new user with the fields from $_GET.
  // Note: The values are filled into $_GET in the email to the admin.
	// If this site is called for the first time, the values will simply be empty.
  $userName = $_GET['username'];
  $passwd   = $_GET['password'];
  $passwd2  = $_GET['password2'];
  $email    = $_GET['email'];

} else { // 'submit' gedrueckt

  $userName = $_POST['username'];
  $passwd   = $_POST['password'];
  $passwd2  = $_POST['password2'];
  $email    = $_POST['email'];
}

/***************************
 * Starttext konfigurieren
 * Ggf. Benutzer anlegen
 ***************************/
if(!isset($_POST['submit'])) {
  $text = "Hier kannst Du einen neuen Benutzer anlegen.";

} else { // 'Submit' wurde gedrueckt
  $err = dataOk();
	if ($err !== true) {
		$text = "<font color=\"red\">$err</font>";
	} else { // Felder erfolgreich gefuellt
	
		// Benutzer anlegen 
		// (Wenn hierbei Fehler passieren, wird $err neu gesetzt, sodass man das weiter unten auch noch mitbekommt)
		$query = @mysql_query("SELECT user FROM user WHERE user = '".$_POST['username']."'");
		$result = @mysql_fetch_array($query);
		if($_POST['username'] == $result['user']) {
			$err = "Dieser Benutzername ist leider schon vergeben.";
			$text = "<font color=\"red\">$err</font>";

		} else { // echter neuer Benutzer

			$pass = md5($passwd);
			if($insert = @mysql_query("INSERT INTO user SET user = '$userName', pass = '$pass', EMail='$email'")) {
				$text = "Der Benutzer ". $_POST['username']. " wurde erfolgreich angelegt.";
			} else { // Datenbank-Fehler
				$err = "Datenbank-Fehler! Bitte sp&auml;ter noch einmal probieren!";
				$text = "<font color=\"red\">$err</font>";
			}
		}
	}
}


?>

<form action="<?php $PHP_SELF ?>" method="post">
	<table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
	<tr>
	<td bgcolor="#e7e7e7" align="center" colspan="2"><b><?php echo $text; ?></b></td>
	</tr>
	<tr>
		<td width="170" bgcolor="#e7e7e7">Benutzername</td>
		<td width="230" bgcolor="#ffffff"><input type="text" name="username" size="20" class="input" value="<?php echo $userName; ?>"></td>
	</tr>
	<tr>
		<td width="170" bgcolor="#e7e7e7">Passwort</td>
		<td width="230" bgcolor="#ffffff"><input type="password" name="password" size="20" class="input" value="<?php echo $passwd; ?>"></td>
	</tr>
	<tr>
		<td width="170" bgcolor="#e7e7e7">Passwort wiederholen</td>
		<td width="230" bgcolor="#ffffff"><input type="password" name="password2" size="20" class="input" value="<?php echo $passwd2; ?>"></td>
	</tr>
	<tr>
		<td width="170" bgcolor="#e7e7e7">E-Mail</td>
		<td width="230" bgcolor="#ffffff"><input type="text" name="email" size="20" class="input" value="<?php echo $email; ?>"></td>
	</tr>
	<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2"><input type="submit" name="submit" value="Benutzer anlegen" class="button"></td>
	</tr>
	</table>
</form>

<?php

if (debug()) {
	print "err = $err<br>";
}

if (isset($_POST['submit']) && $err === true) { // Alles OK, Benutzer angelegt

	/*********************************************
	 ** E-Mail an User senden: Account angelegt **
	 *********************************************/
	$url  = root_url(); // URL zum Login
	if (debug()) {
		echo "url = \"$url\"<br>";
	}
		
	/***********************************************
   **  E-Mail-Adresse des Admins suchen,        **
	 **  um diese dem neuen User bekannt zu geben **
	************************************************/
	$Befehl    = "SELECT EMail FROM user where user='admin'";
	$Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
	$ausgabe   = mysql_fetch_array ($Ergebnis);
	$adminsmail = $ausgabe['EMail'];
	
	/********************
	 **  Send E-Mail   **
	 ********************/
	$text="Hallo ". utf8_encode($userName). ", \n
Der beTTer - Administrator hat soeben Deinen Account freigeschaltet:\n
Username: ". utf8_encode($userName). "
Passwort: ". utf8_encode($passwd). "
\n
Hier geht's zum Login: $url\n\n". utf8_encode("
Nach dem LogIn kannst Du Dein Passwort unter MEINE DATEN ändern.\n
Das Passwort wird verschlüsselt gespeichert und kann auch vom\n
Administrator nicht ausgelesen werden. Falls Du es vergisst, musst\n
Du Dir über den Link auf der Startseite ein neues Passwort generieren\n
lassen. Da Du eine E-Mail Adresse angegeben hast, bekommst Du Dein neues\n
Passwort dann per mail zugesandt.\n\n
Wenn Du mit dem Administrator in Kontakt treten willst, so erreichst Du ihn\n
unter der E-Mail-Adresse: $adminsmail\n\n
Viel Erfolg beim Tippen und viel Spass mit beTTer - the better way to bet.\n\n\n");
	 
	mail($email, "Account bei beTTer angelegt", $text, email_header($adminsmail));

	/***************************************************************************************************
	 *  Erstmalig für den Benutzer tippen: alle Spiele mit -1:-1 und Faktor 1.
	 *  ACHTUNG: man sollte zusehen, dass alle Spiele bereits angelegt sind, bevor User dazu kommen!
	 *           Ansonsten würden hier Spiele vergessen!
	 ***************************************************************************************************/		
	$db_userID     = "SELECT USER_ID FROM user WHERE user = '$userName'";
	$result_userID = mysql_query($db_userID);
	$row_userID    = mysql_fetch_assoc($result_userID);
	$userID        = $row_userID[USER_ID];
	
	if (debug()) {
		print "userID = $userID<br>";
	}

  $errCnt = 0;
	$errors = array();
	$db_spiele     = "SELECT * FROM `spiel`";
	$result_spiele = mysql_query($db_spiele);
	$num_spiele = mysql_num_rows($result_spiele);
	while ($row = mysql_fetch_assoc($result_spiele)) {
	
		$spielID = $row[SPIEL_ID];
	
		// Tipp mit aktuellem Spiel und $userID abgeben
		$db_add_tipp = "INSERT INTO tipp SET USER_ID='$userID', SPIEL_ID='$spielID', Tore1='99', Tore2='99', TippPunkte='0', Faktor='1', Spielpunkte='0'";
		$result_add_tipp = mysql_query($db_add_tipp);
		if (!$result_add_tipp) {
	    // FIXME!!! Also get rest of spiel-info above and print it here! $spielID alone does not tell anyone too much... ;)
			//print "Konnte Spiel $spielID nicht f&uuml;r user $userID tippen - schon getippt?<br>";
			$errCnt++;
			array_push($errors, $spielID);
		}
	}

  /******************************************************
	 * Ausgaben: alles erledigt und hat [nicht] geklappt
   ******************************************************/
	echo '<h5>
	        <p align="center"><br>
	          Eine E-Mail wurde an die angegebene Adresse versendet.<br>
	          Der User <i> '. $userName. ' </i> ist somit informiert, dass sein Account angelegt wurde.<br><br>';

	
	if ($errCnt == 0) { // Alle Tipps erfolgreich in der DB gesetzt
		print "Initiale Tipps f&uuml;r user <i> $userName </i> (ID: $userID) abgegeben (jeweils 99:99, Faktor 1).<br>";
	} else { // Fehler beim Setzen in der DB
	  $msg = "Das Setzen der initialen Tipps f&uuml;r user <i> $userName </i>(ID: $userID) hat nicht funktioniert.<br>Bitte dessen Tipps via \"User-Tipps zur&uuml;cksetzen\" setzen!";
	  if ($errCnt == $num_spiele) {
	   str_replace("hat nicht", "hat z.T. nicht", $msg);
		 $msg .= "<br>Folgende Spiele konnten nicht getippt werden:<br>". array_join($errors, "<br>");
		}
		print $msg;
	}
	
  echo '	</p>
        </h5>';
	
}
?>
</div>
</body>
</html>
