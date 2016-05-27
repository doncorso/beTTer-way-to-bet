<?php
/* Meistertipps aller Spieler  */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Die Turniersieger-Tipps"); 
print $head;
?>

<body>
<?php
$menu = create_menu();
print $menu;


/******************************************************
***** Spaeteste Tipp-Abgabe auslesen *****
*******************************************************/
	$dbanfrage = "SELECT Meister_Tipp_Date, Meister_Tipp_Time FROM settings ";                
	$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
	$ausgabe = mysql_fetch_array ($result);

  // check: kann die Tabelle ueberhaupt schon agnezeigt werden oder kann noch getippt werden?
  $heute = today();
  $jetzt = now();
  
  $mtd = $ausgabe['Meister_Tipp_Date'];
  $mtt = $ausgabe['Meister_Tipp_Time'];
  
  if ($heute < $mtd || ($heute == $mtd && $jetzt < $mtt)) {
    $fullMeisterTippTime = date_time_to_full_date($mtd, $mtt);

    print '<div align="center">Hier werden die Turniersieger-Tipps angezeigt, wenn die Tipps nicht mehr ver&auml;ndert werden k&ouml;nnen, also ab:<br><br>
           <b>'. $fullMeisterTippTime. '</b>
           </div>';
    die();
  }


/******************************
TABELLEN SETTINGS
*******************************/
print ('<table align="center" valign="middle" border="10">
			 <colgroup>
			 <col width="30">
			 <col width="150">
			 <col width="50">
			 </colgroup>');
print("<th align=\"center\">Spieler</th><th colspan=\"2\">Meistertipp</th>");

$db_getMTipps = mysql_query("SELECT USER_ID, user, MeisterTipp FROM user WHERE user != 'admin' AND user != 'gast'
                             ORDER BY user");

while ($ausgabe_get_MTipps = mysql_fetch_array ($db_getMTipps))
{   
    $tippUser = $ausgabe_get_MTipps[user];
    $teamID   = $ausgabe_get_MTipps[MeisterTipp];
    if ($teamID == 0) {
	  $teamName = "nicht getippt";
	}else {
	  $db_getTeamName = mysql_query("SELECT Name FROM `team` WHERE TEAM_ID=$teamID");
	  $db_teamName = mysql_fetch_array ($db_getTeamName);
	  $teamName = $db_teamName[0];
	}

	print ("<tr valign=\"middle\" align=\"center\">");
	print ("<td>$tippUser</td>");
	print ("<td><b>$teamName</b></td>");
	print ("<td align=\"center\" valign=\"bottom\"><img src=\"flags/$teamID.gif\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\" alt=\"$teamName\"></td>");
	print ("</tr>");
}
print("</table>");
        
        
?>
                
</body>
</html>
