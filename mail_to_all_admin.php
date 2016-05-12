<?php
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));

$Submit = $_POST["Submit"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>

<?php /* FIXME!!! create_head() anpassen! */ ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Admin sends to all users</title>  <link rel="stylesheet" type="text/css" href="style.css">
<style type="text/css">
<!--
.bold_green {
	color: #00CC00;
	font-weight: bold;
}
.bold_red {
	color: #CC0000;
	font-weight: bold;
}
-->
</style>
</head>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php
/*****************************
** ERSTES LADEN DER SEITE	**
*****************************/
if (!isset($Submit))
{
	?>
	  <form name="form1" method="post" action="">
		<div align="center">
			<p>
			Hier kannst Du eine Mail an alle User schreiben. Beachte bitte, dass an den hier verfassten Text immer ein <b>Header</b>und ein <b>Footer</b> angeh&auml;ngt werden.<br>
			Im Header steht stets <b>"Hallo &lt;Benutzername&gt;,"</b> und im Footer <b>"Kontakt aufnehmen zum Admin &uuml;ber folgende Adresse..."</b>.<br><br>
			</p>
		  <p>
			<textarea name="mailtext" cols="80%" rows="15" id="mailtext"></textarea>
		  </p>
		  <p>
			<input type="submit" name="Submit" value="Senden"> 
		  </p>

		</div>
	  </form>
  <?php
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


	//Admins Mail auslesen
	$Befehl    = "SELECT EMail FROM user where user='admin'";
	$Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
	$ausgabe   = mysql_fetch_array ($Ergebnis);	
	$adminsmail = $ausgabe['EMail'];


	$text = $_POST[mailtext];
	$text.= "\n\n
		Antworten oder sonst Kontakt mit dem Administrator aufnehmen kannst Du über $adminsmail \n
		Hier geht's zum Login: $url\n\n
		Viel Spass mit beTTer - the better way to bet !
	 	===============================================================================================
		\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";	
	

	//Alle User auslesen
	$db_getUsers = "SELECT user, EMail FROM user where user!='admin' AND user!='Gast'";
	$result_users = mysql_db_query ($dbName, $db_getUsers, $connect);

	//solange ein user geholt werden kann
	while ($ausgabe_Users = mysql_fetch_array($result_users))
	{
	
		//check, ob user eine mailadresse hinterlegt hat
		if(	$ausgabe_Users[EMail]!="")
		{
		   $usersmail = $ausgabe_Users['EMail'];	
		   $user_to_mail_name = $ausgabe_Users[user];
		 
		   /***************
		   Mail versenden
		   ***************/	
		   $usertext="Hallo $ausgabe_Users[user]\n\n";
		   $usertext.=$text;
		   mail($usersmail, "Mail von beTTer - the better way to bet", $usertext);
		   //Bestaetigung ausgeben
				 print("<br><p align=\"left\" class=\"bold_green\">E-Mail wurde versandt an: $ausgabe_Users[user]<br>");		
		}
		else
		   print ("<br><p align=\"left\" class=\"bold_red\">Keine E-Mail bekannt von: $ausgabe_Users[user]<br>");		
	}// End of WHILE user kann geholt werden
		
	print("<h5><br><br>");		
	print("Viel Spass weiterhin mit <b>beTTer - the better way to bet</b><br><br>");
	print("</h5>");
} // End of ELSE
?>
</body>
</html>
