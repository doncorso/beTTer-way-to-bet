<?php 
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("User l&ouml;schen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="del_user.php" method="post">

<?php

//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if (isset($_POST['submit']))
{
    /*************************
    User loeschen
    **************************/
    $chosen = $_POST[usrdel];

		$dbanfrage = "	SELECT user, USER_ID
										FROM user
										WHERE USER_ID = $chosen";
                
    $result = mysql_db_query ($dbName, $dbanfrage, $connect);
		$row    = mysql_fetch_assoc($result);
		$user   = $row[user];

    $deluser = "DELETE FROM user where USER_ID=$chosen";
    mysql_query($deluser);

    $deluserstipps = "DELETE FROM tipp where USER_ID=$chosen";
    mysql_query($deluserstipps);
    
    print("<br> - User $user (USER_ID = $chosen) geloescht. - <br>");

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



	   /*********************************************************
	        User zum Loeschen - Drop-Down
	     **********************************************************/
        
        print ("<p align=\"center\"> ");
				print ("<b>Zu l&ouml;schenden User ausw&auml;hlen:</b><br><br>");
				print ("<SELECT NAME='usrdel'>");

        while ($ausgabe = mysql_fetch_array ($result) )
        {
            //admin darf logischerweise nicht geloescht werden ;)
            if  ($ausgabe[user] != "admin")
            {
                print ("<OPTION VALUE='$ausgabe[USER_ID]'>$ausgabe[user]");
            }
        }

        print ("</p> </SELECT> <br><br>");

print("</table>");

        print('<input type="submit" name="submit" value=" L&ouml;schen ">
              </form>');

}//End Of Seite zum ersten mal geladen
        
?>
                
</body>
</html>