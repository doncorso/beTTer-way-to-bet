<?php
/* Admin-Login */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 

<?php 
$head = create_head("Admin"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<!--
<table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center"> 
  <tr> 
    <td bgcolor="#e7e7e7" align="center" valign="top" colspan="2"> 
			<?php echo "<b>Hallo ".$_SESSION['user']."</b>"; ?> 
   </td>
  </tr>
  <tr>
    <td bgcolor="#e7e7e7" align="center" valign="top"> 
			-------------------<br>ALLGEMEIN<br>-------------------- <br>
			<br> 
					
			<a href="mylogin.php">Meine Daten</a>
			<br><br>
					
			<a href="admin_settings.php">Einstellungen zum Turnier</a>
			<br><br>

			<a href="set_meister.php">Turniersieger eingeben</a>
			<br><br>
			
			<a href="ranking.php">Ranking</a>
			<br><br>
		
			<a href="gewinnverteilung.php">Gewinnverteilung</a>
			<br><br>    

			<a href="tipphelp.php">Hilfe zum Tippen</a>
			<br><br>
   </td>
   <td bgcolor="#e7e7e7" align="center" valign="top"> 
			----------------------<br>TEAM<br>---------------------- <br>
			<br>
			<a href="new_team.php">Team anlegen</a>
      <br><br>

			<a href="new_flag.php">Flagge Team zuordnen </a> 
			<br><br>
			
			<a href="ren_team.php">Team umbenennen</a>
			<br><br> 
			
			<a href="del_team.php">Team l&ouml;schen</a>
			<br><br>    

			<a href="file_upload.php">Flaggen hochladen</a> 
			<br><br>			

			<a href="team_upload_by_file.php">Teams aus Datei einlesen</a>
			<br><br>    
	 </td>
  </tr>
  <tr>
    <td bgcolor="#e7e7e7" align="center" valign="top"> 
			----------------------<br>USER<br>---------------------- <br>
			<br>
			<a href="neu.php">Neuen Benutzer anlegen</a>
			<br><br>
			
			<a href="del_user.php">Benutzer l&ouml;schen</a>
			<br><br>

			<a href="init_user_tipps.php">User-Tipps zur&uuml;cksetzen</a>
			<br><br>
			
			<a href="mail_to_all_admin.php">Allen Usern eine Mail schicken</a>
			<br><br>
		</td> 
    <td bgcolor="#e7e7e7" align="center" valign="top"> 
  		----------------------<br>SPIEL<br>--------------------- <br>
			<br>	
			<a href="new_kategorie.php">Kategorie anlegen</a>
			<br><br>
			
			<a href="ren_kategorie.php">Kategorie umbenennen</a>
			<br><br>
			
			<a href="new_game.php">Spiel anlegen</a>
			<br><br>
			
			<a href="chg_game.php">Spiel &auml;ndern</a>
			<br><br>
			
			<a href="del_game.php">Spiel l&ouml;schen</a>
			<br><br>
			
			<a href="set_result.php">Spiel-Ergebnis eingeben</a>
			<br><br>
			
			<a href="spiele_upload_by_file.php">Spiele aus Datei einlesen</a>
			<br><br>			
		</td>
  </tr>
  <tr>
    <td bgcolor="#e7e7e7" align="center" valign="top" colspan="2">	
      <p>
				<a href="logout.php">LogOut</a>
  		</p>
		</td> 		
  </tr> 
</table> 
-->

</body> 
</html>