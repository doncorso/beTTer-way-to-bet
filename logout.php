<?php 
require "general_methods.inc.php";
check_session(false);
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 

<?php 
$head = create_head("Logout"); 
print $head;
?>

<body> 
<?php 
$user = $_SESSION['user']; 
//session_unregister('user');
//session_unset();
if(session_destroy()) {
  echo '<p align="center">Bye bye '.$user.'<br><br>Du hast Dich erfolgreich abgemeldet.<br><br><a href="index.php">Zur 
Anmeldung</a></p>'; 
}else{ 
  echo '<p align="center">Beim Abmelden trat leider ein Fehler auf!<br><br>Bitte schliesse Dein Browserfenster bzw. den aktuellen Tab.'; 
} 
?> 
</body> 
</html> 
