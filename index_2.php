<?php
session_start();
require("connect.inc.php");
require("general_defs.inc.php");
require("general_methods.inc.php");

// -------------------------------------------------------------------------------------------
//	loginOK()
//	Gibt true zurueck, wenn der Login erfolgteich war und andernfalls die Fehlermldung. 
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
function loginOK() {

  $theUser = $_POST['username'];
  if (!$theUser || $theUser == "") return "Einen Benutzernamen brauchen wir schon! ;)";

  $password = $_POST['password'];
  if (!$password || $password == "") return "Ein Passwort brauchen wir schon! ;)";
  
  // No injection in username
  $err = check_login_name($theUser, ILLEGAL_CHARS);
  if ($err !== true) return $err;
  
  // encode password
  $password = md5($password);  

  // check whether user and password are correct
  $query = @mysql_query("SELECT user, pass FROM user WHERE user = '$theUser'");
  if ($query === false) return ('Datenbank-Problem: Select ist fehlgeschlagen! Bitte sp&auml;ter noch einmal probieren!');

  $errText = "Unbekannter Benutzername oder falsches Passwort.<br>"; 
  $result = @mysql_fetch_array($query);
  if (!$result) return $errText;  // Wenn result leer => Benutzer nicht gefunden => Fehler
  if($password != $result['pass']) return $errText; // Passwort falsch => Fehler

  return true; 
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
if(isset($_POST['submit'])) { 

  $ok = loginOK();
  if ($ok === true) {
    /***************************
     * Login korrekt
     ***************************/
    $_SESSION['user']=$_POST['username'];

    echo '
      <head>
        <meta http-equiv="refresh" content="1; URL=sichere_seite.php">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>LogIn </title>
      </head>';  
  } 
}

/***************************
 * Starttext konfigurieren
 ***************************/
if (!isset($_POST['submit'])) {
  // noch keine Eingabe: Initialtext hinschreiben
  $text = "Hier kannst Du Dich anmelden";

} else { 

  if ($ok !== true) {
    // falsche Eingabe: Fehlertext hinschreiben
    // Man beachte: Wenn wir hier ankommen und $_POST['submit'] ist gesetzt, dann kann $ok nur !== true sein!
    $text = "<font color='red'>$ok</font>"; // $ok enthaelt jetzt die Fehlermeldung
  } else {
    // Nichts zu tun hier.
    // Wenn Login korrekt, dann erscheint sowieso ein komplett anderer Text (s.u.) 
  }
} 

// Standard-<head> schreiben (d.h. wenn noch keine bzw. fehlerhafte Eingabe)
if (!isset($_POST['submit']) || $ok !== true) {

  echo '
    <head>
      <title>LogIn </title>
      <link rel="stylesheet" type="text/css" href="style.css">
      <style type="text/css">
      <!--
      .Stil1 {
      	font-size: 16pt;
      	font-weight: bold;
      	color: #000066;
      }
      .Stil2 {font-size: 9pt}
      -->
      </style>
    </head>';
} 

?>
<body>

<?php
 if (isset($_POST['submit']) && $ok === true) { // Login korrekt
  echo '
    <p align="center">
      LogIn erfolgreich! :-)<br><br><br>
      Du wirst weitergeleitet ...<br><br>
      ...sonst klicke <a href="sichere_seite.php">hier</a>
    </p>';
  } else { // Noch keine bzw. fehlerhafte Eingabe
?>
    
  <form action="<?php $PHP_SELF?>" method="post">
    <p>&nbsp;</p>
    <p align="center" class="Stil1">Welcome to beTTer - the better way to bet</p>
    <p>&nbsp;</p>
  
    <table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
    <tr>
      <td bgcolor="#e7e7e7" align="center" colspan="2">
      <b><?php echo $text; ?></b>
      </td>
    </tr>
    <tr>
      <td width="170" bgcolor="#e7e7e7">Benutzername</td>
      <td width="230" bgcolor="#ffffff"><input type="text" name="username" size="20" class="input" <?php echo "value=\"". $_POST['username']. "\""; ?>></td>
    </tr>
    <tr>
      <td width="170" bgcolor="#e7e7e7">Passwort</td>
      <td width="230" bgcolor="#ffffff"><input type="password" name="password" size="20" class="input"></td>
    </tr>
    <tr>
      <td bgcolor="#e7e7e7" align="center" colspan="2">
      <input type="submit" name="submit" value="Anmelden" class="button">
      </td>
    </tr>
  </table>
  <p align=center> <img src=flags/Logo.gif> 
  </p>
  <p align=center>
  Noch keinen Account?
  <br> Mit Benutzername <b>Gast</b> und Passwort <b>gast</b>
  <br> kannst Du schonmal schauen, wie es drinnen so aussieht, 
  <br>und Du kannst Dich dort auch f&uuml;r einen User-Account anmelden.<br><br><br>
  - <a href="forgot_pass.php">Passwort vergessen</a> -
  <br><br><br>
  
  <b>Wichtig:</b><br>
  Wenn Du Dich nicht anmelden kannst, so liegt das daran, <br> 
  dass die Einstellungen Deines Browsers das Anlegen einer Session verhindern.<br>
  Klicke in diesem Fall bitte<a href="<?php echo $_SERVER['PHP_SELF']; ?>" target=_blank> - hier - </a>um Dich anmelden zu k&ouml;nnen.<br>
  
  </form>
  <p align="center"><br>beTTer - the better way to bet (Version <?php echo VERSION; ?>).</p>
  
<?php 
} // Ende noch keine bzw. fehlerhafte Eingabe 
?>  
</body>
</html>
