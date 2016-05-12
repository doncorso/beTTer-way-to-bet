<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>- Setup.php l&ouml;schen -</title>
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
if (file_exists("setup.php"))
{
	if(unlink("setup.php"))
		print("<br> Datei <strong>setup.php</strong> wurde erfolgreich gel&ouml;scht!<br>
		Sie stellt nun kein Sicherheitsrisiko mehr f&uuml;r Ihre Tipprunde dar !");
		else
		print("<br> FEHLER: Die Datei <strong>setup.php</strong> konnte nicht gel&ouml;scht werden!<br>
		Bitte l&ouml;schen Sie sie von Hand, um das angesprochene Sicherheitsrisiko zu beseitigen!");	
}
else
	print("<br> KEIN PANIK: Die Datei <strong>setup.php</strong> existiert gar nicht mehr!<br>");	

print("<b>beTTer</b> ist fertig eingerichtet. </p><br>Gehen Sie nun zur <b><a href=\"index.php\">index.php</a></b></p> und loggen sich als admin ein, um Spiele und Benutzer anzulegen.<br>");
print("<br><br>");
print("<p align =\"center\"><span class=\"Stil3\">Viel Spass mit beTTer - the better way to bet<br></p>");

?>
</body>
</html>
