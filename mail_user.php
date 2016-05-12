<?php
/* Einem Mitspieler bzw. dem Admin eine Mail schreiben */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);

// Ermitteln, wem eine Mail geschickt werden soll 
// (aus $_GET UND aus $_POST - je nachdem, ob die Seite per Link aufgerufen wurde oder ob man gerade einen User manuell ausgewaehlt hat)
$user_to_mail_ID = isset($_GET["uid"])? $_GET["uid"] : $_POST["uid"];
if (debug()) { print "user_to_mail_ID = $user_to_mail_ID<br>"; }

if ($user_to_mail_ID) {
	
	$db_getUser_info = "SELECT user FROM user WHERE USER_ID=$user_to_mail_ID";
	$result_userinfo = mysql_db_query ($dbName, $db_getUser_info, $connect);
	$ausgabeInfo = mysql_fetch_array($result_userinfo);
	
	$user_to_mail_name = $ausgabeInfo[user];
	if (debug()) { print "user_to_mail_name = $user_to_mail_name<br>"; }

} else { // $_GET["uid"] und $_POST["uid"]nicht gesetzt

  // Hole alle Benutzer ausser man selbst, admin und Gast
  $db_getUsers = "SELECT * FROM user WHERE user != \"admin\" && user != \"Gast\" && user != \"". $_SESSION['user']. "\" ORDER BY user ASC";
  $result_users = mysql_db_query ($dbName, $db_getUsers, $connect);
}

$submit = $_POST["Submit"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<?php /* FIXME!!! create_head() anpassen!!! */ ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>send Mail to User</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?php
$menu = create_menu();
print $menu;

/*****************************
** ERSTES LADEN DER SEITE	**
** (mit oder ohne uid)    **
*****************************/
if (debug()) { print "submit = $submit<br>"; }
if (!isset($submit) || $submit != "Senden")
{
	if (!$user_to_mail_ID) {

	  echo '<form name="form1" method="post" action="">';
		echo '<div align="center">';
		echo 'Bitte Benutzer ausw&auml;hlen:<br>';
		echo '<p><select name="uid" id="uid">';
    
		while ($ausgabe = mysql_fetch_assoc($result_users)) {
		  echo '<option value="'. $ausgabe[USER_ID]. '">'. $ausgabe[user]. '</option>';
   	}

		echo '</select></p>';
		echo '<p><input type="submit" name="Submit" value="Benutzer ausw&auml;hlen"></p>';
	  echo '</form>';
	} else { // $user_to_mail_ID was set

		echo '
		<form name="form1" method="post" action="">
 		<div align="center">
		  <p>
			<textarea name="mailtext" cols="80%" rows="15" id="mailtext">Hallo '. $user_to_mail_name. ',

'. $_SESSION[user]. '
      </textarea>
		  </p>
		  <p><input type="submit" name="Submit" value="Senden"></p>
		  <p>
        <input name="mail_to" type="hidden" id="mail_to" value="'. $user_to_mail_ID. '">  
        <input name="mail_from" type="hidden" id="mail_from" value="'. $_SESSION[user]. '">
			</p>
		</div>
	  </form>';
	}
}  // End of SUBMIT is NOT set
else

/*************************
** Absenden angeklickt	**
*************************/
{

  
/****************************
** Mail - Parameter setzen **
*****************************/
	
	//URL zum LogIn zusammenfuegen fuer inhalt im mailversand
	$url  = "http://";
	$url .= $_SERVER['HTTP_HOST'];
	$url .= $_SERVER['PHP_SELF'];
	$weg = strrchr($url,"/"); //eigene PHP-Datei loeschen, damit auf index verwiesen wird
	$url = str_replace($weg,"",$url); 

	$mail_to	 = $_POST[mail_to];
	$mail_from = $_POST[mail_from];
	$mailtext	 = $_POST[mailtext];

	$Befehl     = "SELECT EMail FROM user where user='admin'";
	$Ergebnis   = mysql_db_query ($dbName, $Befehl, $connect);
	$ausgabe    = mysql_fetch_array ($Ergebnis);	
	$adminsmail = $ausgabe['EMail'];

	$Befehl    = "SELECT EMail, user FROM user where USER_ID=$mail_to";
	$Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
	$ausgabe   = mysql_fetch_array ($Ergebnis);	
	
	$usersmail = $ausgabe['EMail'];
	$user_to_mail_name = $ausgabe[user];

	$text="Hallo $user_to_mail_name, diese Mail sendet Dir $mail_from von beTTer - the better way to bet:\n\n";
	$text.=$mailtext;	
	//Wenn nicht der admin geschickt hat, auf Kontakt bei SPAM etc. aufmerksam machen
	if ($mail_from != "admin") {
		$text.="\n 
Falls es sich bei dieser Mail um Spam oder eine sonstige unerwünschte Belästigung handelt, \n
nimm bitte mit dem Administrator ($adminsmail) Kontakt auf!
Hier geht's zum Login: $url";
	}
	$text.="\n
============================================================================================
\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";	
	 
   /***************
   Mail versenden
   ***************/
	//EInmal die eigentlich mail an den user
   mail($usersmail, "Mail von $mail_from von beTTer - the better way to bet", $text);

	// Dann noch eine Info an den Admin, dass ein user an den anderen eine message geschickt hat
	// nur, wenn der admin die nich selbst verschikt hat:
	if ($mail_from != "admin")
	{
	  mail($adminsmail,
   		"$mail_from hat message an $user_to_mail_name verschickt",
			"$mail_from hat message an $user_to_mail_name verschickt\n
Hier geht's zum Login: $url\n\n======================================================\n\n\n\n\n\n\n\n\n\n");   
	}
		
	print ("<h5>");
	echo '<br><p align="center">E-Mail wurde versandt!<br>';
	print("Viel Spass weiterhin mit <b>beTTer - the better way to bet</b><br><br>");
	print("</h5>");
} // End of ELSE (Absenden angeklickt)
?>
<p align="center">&nbsp;</p>
</body>
</html>
