<?php
/*
** Hier kann der Admin den Namen bestehender Kategorien aendern
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spielkategorie umbenennen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php
$db_anfrage = "SELECT * FROM kategorie";
$result = mysql_db_query ($dbName, $db_anfrage, $connect);

/*
** AENDERN   N I C H T  angeklickt
*/
if(!isset($_POST['submit'])) { ?>
<form action="<?php $PHP_SELF ?>" method="post">
<table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
<tr>
<td bgcolor="#e7e7e7" align="center" colspan="2">
<b>Kategorie umbenennen</b>
</td>
</tr>
<tr>
	<td>
	Kategorie auswählen
	</td>	
	<td><select name="kategorie">
	<?php
	/*
	** Drop-Down menue zum Anzeigen aller Kategorien - Wert ist die Kategorie - ID !!
	*/
	while ($ausgabe= mysql_fetch_array ($result))
	{
	  print("<option value=\"$ausgabe[KATEGORIE_ID]\">$ausgabe[Kategoriename]</option>");
	}
	?>
	  </select>
	
	</td>
</tr>
<tr>
	<td width="170" bgcolor="#e7e7e7">Neue Bezeichnung</td>
	<td width="230" bgcolor="#ffffff"><input type="text" name="name" class="input" size="20"></td>
</tr>
<tr>
	<td bgcolor="#e7e7e7" align="center" colspan="2">
	<input type="submit" name="submit" value="Bezeichnung ändern" class="button">
	</td>
</tr>
</table>
</form>
<?php

}else{
	
	/** Bezeichnung AENDERN geklickt  **/

	$name   = $_POST['name'];
	$ID		= $_POST['kategorie'];
	
	if($insert = @mysql_query("UPDATE kategorie SET Kategoriename = '$name' WHERE KATEGORIE_ID='$ID'")) 
	{
		echo '<p align="center">Die Kategorie wurde erfolgreich umbenannt!<br><br><a href="admin.php"></p>';
	}else
	{
		echo '<p align="center">Beim Umbennen trat leider ein Fehler auf!<br><br></p>';
	}	
} //End of CLICKED 

?>
</body>
</html>
