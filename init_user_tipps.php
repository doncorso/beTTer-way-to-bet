<?php 
/*
Hier kann der admin die Tipps eines users zurücksetzen.
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("User-Tipps zur&uuml;cksetzen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="init_user_tipps.php" method="post">

<?php

//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if (isset($_POST['submit']))
{
 	  /************************
	    User-Tipps zurücksetzen
	   *************************/
    $userID = $_POST[usrtr];

    // Hole die Spiele von $userID oder von allen usern($userID == "Alle")
		$where = "WHERE USER_ID = $userID";
		if ($userID == "Alle") {
			$where = "WHERE user != 'gast' AND user != 'admin'";
		}

		$dbanfrage = "	SELECT user, USER_ID
										FROM user
										$where";
                
    $result_user = mysql_db_query ($dbName, $dbanfrage, $connect);

    // Hole alle Spiele
		$db_spiele     = "SELECT * FROM `spiel`";
		$result_spiele = mysql_query($db_spiele);

		while($rowUser = mysql_fetch_assoc($result_user)) {
		
		  $user          = $rowUser[user];
			$currentUserID = $rowUser[USER_ID];

			if (debug()) {
				print "{ user, userID, currentUserID } = { $user, $userID, $currentUserID }<br>";
			}
		
			while ($rowSpiele = mysql_fetch_assoc($result_spiele)) {
			
				$spielID = $rowSpiele[SPIEL_ID];
			
				// Tipp mit aktuellem Spiel und $userID abgeben
				$db_add_tipp = "INSERT INTO tipp SET USER_ID='$currentUserID', SPIEL_ID='$spielID', Tore1='99', Tore2='99', TippPunkte='0', Faktor='1', Spielpunkte='0'";
				$result_add_tipp = mysql_query($db_add_tipp);
				if (!$result_add_tipp) {
					$db_update_tipp = "UPDATE tipp 
														 SET Tore1='99', Tore2='99', TippPunkte='0', Faktor='1', Spielpunkte='0'
														 WHERE USER_ID = $currentUserID and SPIEL_ID=$spielID";
					$result_update_tipp = mysql_query($db_update_tipp);
					if (!$result_update_tipp) {
						print "Konnte den Tipp von user $currentUserID f&uuml;r das Spiel mit der ID $spielID nicht &auml;ndern. Bitte noch einmal probieren!<br>";
					}
				}
			}
			print("<br> - Alle Tipps von User $user (USER_ID = $currentUserID) zur&uuml;ckgesetzt. - <br>");
			
			// reset internal data pointer (otherwise, the next loop does nothing)
			mysql_data_seek($result_spiele, 0);
		}
    
    print("<br><br><br>");

}
//Wenn Seite zum ersten mal geladen wurde (kein Submit)
else
{

	$dbanfrage = "	SELECT user, USER_ID
			FROM user
			WHERE user != 'admin' AND user != 'Gast' 
			ORDER BY user ASC";
                
  $result = mysql_db_query ($dbName, $dbanfrage, $connect);

  /********************************************************
    User - Drop-Down
   *********************************************************/
        
  print ("<p align=\"center\">");
  print ("<b>User ausw&auml;hlen:</b><br><br>");
	print ("<SELECT NAME='usrtr'>");
	while ($ausgabe = mysql_fetch_array ($result)) {
		print ("<OPTION VALUE='". $ausgabe[USER_ID]. "'>". $ausgabe[user]);
	}
	
	print ("<OPTION VALUE='Alle'>Alle");
	print ("</p> </SELECT> <br><br>");

	print("</table>");
	print('<input type="submit" name="submit" value=" Tipps zur&uuml;cksetzen ">');
	print("</form>");

}//End Of Seite zum ersten mal geladen
        
?>
                
	</body>
</html>