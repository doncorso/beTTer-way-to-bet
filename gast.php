<?php 
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("Gast"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 

<?php
$head = create_head("Gast");
print $head;
?>

<body> 

<?php
$menu = create_menu();
print $menu;
?>
</body> 
</html>