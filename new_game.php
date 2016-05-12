<?php
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Neues Spiel anlegen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php if(!isset($_POST['submit'])) { ?>
<form action="<?php $PHP_SELF ?>" method="post">
<table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
<tr>
<td bgcolor="#e7e7e7" align="center" colspan="2"><b>Neues Spiel anlegen</b></td>
</tr>
<tr>
<td width="170" bgcolor="#e7e7e7">Datum (JJJJ-MM-TT)</td>
<td width="230" bgcolor="#ffffff"><input type="text" name="datum" class="input" size="20"></td>
</tr>

<tr>
<td width="170" bgcolor="#e7e7e7">Anpfiff (HH:MM)</td>
<td width="230" bgcolor="#ffffff"><input type="text" name="anpfiff" class="input" size="20"></td>
</tr>
        

<tr>        
<?php        
        /** TEAM 1 **/        
        
$db_getTeams = "SELECT TEAM_ID, Name FROM team ORDER BY Name";
$resultT = mysql_query($db_getTeams);

        print ("<td width=\"170\" bgcolor=\"#e7e7e7\">Team 1</td>
        
        <td> <SELECT NAME='Team_1'>");
        
while ($ausgabeTeams = mysql_fetch_array ($resultT) )
{
	//Team-ID 0 ist reserviert fuer NICHT BEKANNT wegen Finalbegegnungen
        if  ($ausgabeTeams[TEAM_ID] != 0)
          {
           print ("<OPTION VALUE='$ausgabeTeams[TEAM_ID]'>$ausgabeTeams[Name]");
          }
}
print ("</td> </SELECT></tr>");
        ?>
        


<?php        
        /** TEAM 2 **/        
        
$db_getTeams = "SELECT TEAM_ID, Name FROM team ORDER BY Name";
$resultT = mysql_query($db_getTeams);

        print ("<td width=\"170\" bgcolor=\"#e7e7e7\">Team 2</td>
        
        <td> <SELECT NAME='Team_2'>");
        
while ($ausgabeTeams = mysql_fetch_array ($resultT) )
{
	//Team-ID 0 ist reserviert fuer NICHT BEKANNT wegen Finalbegegnungen
        if  ($ausgabeTeams[TEAM_ID] != 0)
          {
           print ("<OPTION VALUE='$ausgabeTeams[TEAM_ID]'>$ausgabeTeams[Name]");
          }
}
print ("</td> </SELECT>");
?>     
        
</tr>        

<tr>        
<?php        
        /***************** Kategorie *********************/        
        
$db_getKategorien = "SELECT KATEGORIE_ID, Kategoriename FROM kategorie ORDER BY Kategoriename";
$resultK = mysql_query($db_getKategorien);

        print ("<td width=\"170\" bgcolor=\"#e7e7e7\">Kategorie</td>
        
        <td> <SELECT NAME='Kategorie'>");
        
while ($ausgabeKategos = mysql_fetch_array ($resultK) )
{
     print ("<OPTION VALUE='$ausgabeKategos[KATEGORIE_ID]'>$ausgabeKategos[Kategoriename]");
          
}
print ("</td> </SELECT></tr>");
        ?>
        


<tr>
<td bgcolor="#e7e7e7" align="center" colspan="2">
<input type="submit" name="submit" value="Spiel anlegen" class="button">
</td>
</tr>
</table>
</form>
<?php

}else{

/** SPIEL ANLEGEN geklickt  **/

$datum 		= $_POST['datum'];
$anpfiff 	= $_POST['anpfiff'];
$team1 		= $_POST['Team_1'];
$team2 		= $_POST['Team_2'];
$kategorie 	= $_POST['Kategorie'];

/* print("$datum - $anpfiff - $team1 - $team2 <p>"); */


if($insert = @mysql_query("INSERT INTO spiel SET Datum = '$datum', Anpfiff = '$anpfiff', Team1 = '$team1', Team2 = '$team2', Kategorie='$kategorie' ")) {
echo '<p align="center">Das neue Spiel wurde erfolgreich angelegt!</p>';
/*************************************************************************
** Anzahl vergebbarer Faktorpunkte MUSS mindestens ANZAHL Spiele sein ! **
*************************************************************************/
	// Wieviele Faktorpunkte sind vergebbar
	$dbanfrage_FP = "SELECT Anzahl_Faktorpunkte
					FROM settings";
	$result_FP	  = mysql_db_query ($dbName, $dbanfrage_FP, $connect);
	$ausgabe_FP	  = mysql_fetch_array ($result_FP);
	$Anzahl_Faktorpunkte = $ausgabe_FP[Anzahl_Faktorpunkte];
	//Wieviele Spiele sind jetzt eingetragen:
	$dbanfrage_SP = "SELECT COUNT(*) FROM spiel";
	$result_SP	  = mysql_db_query ($dbName, $dbanfrage_SP, $connect);
	$ausgabe_SP	  = mysql_fetch_array ($result_SP);	
	$Anzahl_Spiele = $ausgabe_SP[0];
	
	if ( $Anzahl_Faktorpunkte < $Anzahl_Spiele)
	{
	   $Anzahl_Faktorpunkte = $Anzahl_Spiele;
	  /****************************************************************************************************************
	   * eingegebene Anzahl Faktorpunkte muessen als neues DEFAULT fuer die Spalte KONTO bei der Tab USER gesetzt werden
	   *****************************************************************************************************************/
   	   $update_default_Konto="ALTER TABLE `user` CHANGE `Konto` `Konto` INT( 11 ) DEFAULT '$Anzahl_Faktorpunkte' NOT NULL";
	   mysql_query($update_default_Konto);
	 	   
	   /*************************************************************************************************
	   ** Jetzt noch die neuen Faktorpunkte bei allen bisher angelegten Spielern aendern (USER->KONTO **
	   *************************************************************************************************/
	    $update_users_Konto="UPDATE user SET Konto ='$Anzahl_Faktorpunkte' WHERE user != 'admin'";
   		mysql_query($update_users_Konto);
		   
	   /************************************************************************************************************************
	   ** Und zu guter letzt sollte man nicht vergessen, die neuen Faktorpunkte auch in die Tabelle SETTINGS zu schreiben !! **
	   ************************************************************************************************************************/
	    $update_settings="UPDATE settings SET Anzahl_Faktorpunkte ='$Anzahl_Faktorpunkte'";
   		mysql_query($update_settings);
   
   
	 	print("<br> Die vergebbaren Faktorpunkte mussten auf $Anzahl_Faktorpunkte erh&ouml;ht werden, <br>um die Vorraussetzung zu erfüllen, dass<br>
		Anzahl Faktorpunkte mindestens gleich Anzahl Spiele ($Anzahl_Spiele) ist !!<br>
		Sollen die Spieler MEHR Faktorpunkte erhalten, so stellen Sie das bitte unter \"EINSTELLUNGEN ZUM TURNIER\" im Admin Menue ein");
	}
	
	echo '<hr><br><br><p align="center"><a href="new_game.php">Noch ein Spiel anlegen</a></p>';
	echo '<p align="center"><br><a href="admin.php">Zum Men&uuml;</a></p>';

}else{
echo '<p align="center">Beim Anlegen des neuen Spiels trat leider ein Fehler auf!</p>';
}

    
    
    
}

?>
</body>
</html>
