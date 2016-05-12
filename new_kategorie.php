<?php
/*
** Hier kann der Admin neue Kategorien fuer die Spiele anlegen.
** beispielsweise: Spieltag 1 - Spieltag 2 - Vorrunde - Halbfinale - etc.
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Neue Spielkategorie anlegen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php if(!isset($_POST['submit'])) { ?>
<form enctype="multipart/form-data" action="<?php $PHP_SELF ?>" method="post">
	<table width="600" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<b>Neue Kategorie für Spiele anlegen</b>
		</td>
		</tr>
		<tr>
			<td width="170" bgcolor="#e7e7e7">Kategoriebezeichnung<br>(Spieltag 1, Vorrunde, ...)</td>
			<td width="230" bgcolor="#ffffff"><input type="text" name="name" class="input" size="20"></td>
		</tr>
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<input type="submit" name="submit" value="Kategorie anlegen" class="button">
		</td>
		</tr>
	</table>
</form>
<?php

}else{

	/** KATEGORIE ANLEGEN geklickt  **/	
	$name = $_POST['name'];
	
	/***********************************************
	**  checken, ob Kategoriename schon vergeben  **
	***********************************************/
	$dbanfrage	 = "SELECT COUNT(*)
					FROM kategorie WHERE Kategoriename='$name'";
	$result		  = mysql_db_query ($dbName, $dbanfrage, $connect);
	$ausgabe	  = mysql_fetch_array ($result);	
	$Anzahl_Kategs = $ausgabe[0];
	
	if ($Anzahl_Kategs != 0)
	{
		print("<br>Eine Kategorie mit der Bezeichnung \"$name\" ist schon vorhanden. <br>
		Zwei Kategorien mit demselben Namen wuerden eine eindeutige Zuordnung unmoeglich machen ...<br><br>");
	}
	
	/************************************************************************
	** Wenn es noch keine Kategorie mit diesem Namen gibt: anlegen !!!
	**************************************************************************/
	else{
		
		if($insert = @mysql_query("INSERT INTO kategorie SET Kategoriename = '$name'")) 
		{
			print("<p align=\"center\">Die Kategorie \"$name\" wurde erfolgreich angelegt!</p>");
		}else {
			echo '<p align="center">Beim Anlegen der neuen Kategorie trat leider ein Fehler auf!</p>';
		}	
		
	
	} // End of IF KATEGORIENAME NOCH NICHT VORHANDEN
	
		
} //End of CLICKED 

?>
</body>
</html>
