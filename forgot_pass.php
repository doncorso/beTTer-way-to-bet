<?php
/* HIER KANN DER USER SEINEN LOGIN AENDERN  */

require("connect.inc.php");


function create_new_password() { 
    $newpass = ""; 
    $laenge=10; 
    $string="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"; 

    mt_srand((double)microtime()*1000000); 

    for ($i=1; $i <= $laenge; $i++) { 
        $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1); 
    } 
     
    return $newpass; 
} 


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Passwort vergessen</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>


<?php

/*********************************
*** SUBMIT wurde nicht gedrueckt
*********************************/

//URL zum LogIn zusammenfuegen fuer inhalt im mailversand
$url  = "http://";
$url .= $_SERVER['HTTP_HOST'];
$url .= $_SERVER['PHP_SELF'];
$weg = strrchr($url,"/"); //eigene PHP-Datei loeschen, damit auf index verwiesen wird
$url = str_replace($weg,"",$url); 




if(!isset($_POST['submit'])) { 
?>
<h3 align="center">Passwort vergessen?</h3>
<div align="center"><br>
</div>
<h4 align="center">Kein Problem: Einfach Benutzernamen eingeben und<br>
ein neues Passwort wird an die von Dir angegebene E-Mail-Adresse verschickt</h4>
<form action="<?php $PHP_SELF?>" method="post">
<table width="25%" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center"> 
  <tr> 
    <td bgcolor="#e7e7e7" align="center" colspan="2"> 
    <b>Bitte gib Deinen Benutzernamen ein</b> 
    </td> 
  </tr> 
  
    <tr> 
    <td width="30%">username</td> 
    <td><input type="text" name="username" size="20" class="input"
        <?php echo ' value=""'; ?>>
      </td> 
  </tr>
 
 <tr> 
    <td align="center" colspan="2"> 
    <input type="submit" name="submit" value="Passwort neu erstellen" class="button"> 
    </td> 
  </tr> 
</table>

</form> 

<?php 

}


/*********************************
***** SUBMIT wurde gedrueckt *****
*********************************/

elseif(!isset($_POST['username']) || $_POST['username'] == "") 
{
	?>
	<p align="center"> <br>Benutzernamen musst DU schon angeben !<br><br>
	<a href="forgot_pass.php">-zurück-</a><br>
	<br>
	<?php
} 
else
{
    /*
    * E-Mail - Adresse zum eingegebenen User einlesen
    */
    $Befehl    = "SELECT EMail FROM user where user='$_POST[username]'";
    $Ergebnis  = mysql_db_query ($dbName, $Befehl, $connect);
    $ausgabe   = mysql_fetch_array ($Ergebnis);

    $usersmail = $ausgabe['EMail'];
	if($usersmail=="")
	{
		print("<br>Es konnte keine Adresse zu dem User $_POST[username] gefunden werden<br>");
	}
	else
	{
		$new_pass 	  = create_new_password();
		$new_pass_md5 = md5($new_pass);
		
	
	
		 	/*
			 * Send E-Mail
			 */
		$text="Du hast bei beTTer angegeben, dass Du Dein Passwort vergessen hast. \n
	  	Daher wurde Dein Passwort auf\n
		\n
		$new_pass
		gesetzt.\n
		Bitte aendere es nach dem ersten LogIn unter MEINE DATEN ab.\n\n
		Viel Erfolg beim Tippen und weiterhin viel Spass mit beTTer\n
		Hier geht's zum Login: $url\n\n\n\n\n\n
	  	";
	   
	    mail($usersmail, "Neues Passwort fuer beTTer", $text);
			/*
			 * DB - Update
			 */
		$set_new_email= "UPDATE user SET pass='$new_pass_md5' WHERE user='$_POST[username]' ";
        mysql_query($set_new_email);    
		
	    print ("<h5>");
	    echo '<br><p align="center"><br>';
	    print("Eine E-Mail wurde an die unter dem Benutzer $_POST[username] gespeicherte Adresse versendet.<br>
		Die Zustellung kann aus technischen Gruenden bis zu 20 Minuten dauern.<br>");
	    print("</h5>");
	  }
	?>
	<p align="center"> <br><br>
	<a href="forgot_pass.php">-zurück-</a><br>
	<br> </p>
	<?php
} 
?> 
<p align="center"><br><br> <a href="index.php" target="_top">-zum LogIn-</a> </p>

</body> 
</html> 
