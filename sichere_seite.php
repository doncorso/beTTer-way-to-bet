<?php
/* Hauptmenue  */
require "general_methods.inc.php";
check_session(true, array("admin", "Gast"));
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Sichere Seite"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<p align="center">
	<iframe name="ChatFrame"  border="0" frameborder="0" width="459" height="426" src="framestart.php">
	Ihr Browser unterst&uuml;tzt Inlineframes nicht oder zeigt sie in der derzeitigen Konfiguration nicht an.
	</iframe>
</p>
</body> 
</html>
