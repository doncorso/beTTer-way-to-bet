<?php
/* Meine Daten  */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array("Gast"));

// Zur Vereinfachung
$user = $_SESSION['user'];

// -------------------------------------------------------------------------------------------
//	dataOK()
//	Gibt true oder einen Fehler-String zurueck.
//  Diese Methode steht derzeit nicht in den general_methods.inc.php, weil dort sonst 
//  connect.inc.php required waere.
//  
//  Ausserdem spart man sich (fast) alle Parameter, weil die hier eh bekannt sind.
//  Einzige Ausnahme:
//    $old_pass: Das alte Passwort. Einfacher zu uebergeben als die DB-Abfrage hier direkt zu machen,
//               da die Variablen aus connect.inc.php hier im function()-Kontext nicht bekannt sind.
//
//  TODO: Ueberlegen, ob es Sinn hat, connect.inc.php in genral_method.inc.php zu includen! 
//        Gibt es noch andere DB-Abfragen, die man dort gerne haette? 
//        Problem: Ohne connect.inc.php koennte man dann auch general_method.inc.php
//        nicht mehr includen!
// -------------------------------------------------------------------------------------------
function dataOK($old_pass) {

  if ((!$_POST['email'])   || ($_POST['email']   == "") ||
      (!$_POST['pw_old'])  || ($_POST['pw_old']  == "") ||
      (!$_POST['pw_new1']) || ($_POST['pw_new1'] == "") ||
      (!$_POST['pw_new2']) || ($_POST['pw_new2'] == "")) {
 	  return "Bitte alle Felder ausf&uuml;llen."; 
 	}
        
  if($_POST['pw_new1'] != $_POST['pw_new2']) {
    return "Die Passw&ouml;rter stimmen nicht &uuml;berein.";
  }

  $err = check_login_name($_POST['pw_new1'], " "); // only illegal character for password is space
  if ($err !== true) return str_replace("Ung", "Passwort: ung", $err);

  // check old password
	if (md5($_POST['pw_old']) != $old_pass ) {
		return "Falsches Passwort!";
  }
  
	// all ok
  return true;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Meine Login-Daten"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;

if(!isset($_POST['submit'])) {
  $text = "Hier kannst Du Deine Login-Daten &auml;ndern";

} else { // 'Submit' wurde gedrueckt

  // Altes Passwort holen
	$Befehl    = "SELECT pass FROM user WHERE user='$_SESSION[user]'";
	$Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
	$ausgabe   = mysql_fetch_array ($Ergebnis);
	$old_pass  = $ausgabe['pass'];

  $err = dataOK($old_pass);
	if ($err !== true) {
		$text = "<font color=\"red\">$err</font>";
	} else {
		$text = "Deine Daten wurden erfolgreich ge&auml;ndert.";
		
		// Die Aenderungen auch in die Datenbank einpflegen
		$pw_new1  = $_POST['pw_new1'];
		$email    = $_POST['email'];
		$pw_new   = md5($pw_new1);
		
		$DataUpdate = "UPDATE user SET pass='$pw_new', EMail='$email' WHERE user='$user'";
		mysql_query($DataUpdate);            
	}
}

// Bisherigen (bzw. neuen, wenn alles korrekt) DB-Inhalt zum User einlesen
$Befehl    = "SELECT EMail FROM user where user='$user'";
$Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
$ausgabe   = mysql_fetch_array ($Ergebnis);
$email     = $ausgabe[0];
?>

<form action="<?php $PHP_SELF?>" method="post">
<table width="25%" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center"> 
  <tr> 
    <td bgcolor="#e7e7e7" align="center" colspan="2"><b><?php echo $text; ?></b></td> 
  </tr> 
  <tr> 
    <td width="10%">Username</td> 
    <td><?php echo $user; ?></td> 
  </tr> 
  <tr> 
    <td width="10%">E-Mail</td> 
    <td><input type="text" name="email" size="50" class="input" <?php echo ' value="'.$email.'"'; ?>></td> 
  </tr>
  <tr> 
    <td>Passwort bisher</td> 
    <td><input type="password" name="pw_old" size="50" class="input" <?php echo ' value=""'; ?>></td> 
  </tr> 
  <tr> 
    <td>neues Passwort</td> 
    <td><input type="password" name="pw_new1" size="50" class="input" <?php echo ' value=""'; ?>></td> 
  </tr> 
  <tr> 
    <td>neues Passwort wiederholen</td> 
    <td><input type="password" name="pw_new2" size="50" class="input" <?php echo ' value=""'; ?>></td> 
  </tr> 

<?php if (!(isset($_POST['submit']) && $err === true)) { // korrekte Eingabe: kein "Absenden"-Button anzeigen ?>                                
  <tr> 
    <td align="center" colspan="2"><input type="submit" name="submit" value="Speichern" class="button"></td> 
  </tr> 
<? } // Ende korrekte Eingabe ?>
</table>

</form> 
</body> 
</html> 

