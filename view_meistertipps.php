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
