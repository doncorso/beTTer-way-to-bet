<?php 
/*****************************************************************************************************************************
** Hier kann der administrator Einstellungen zum Turnier vornehmen, wie: Punkteverteilung der Tipps, Name des Turniers usw. **
******************************************************************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Flaggen hochladen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php

//echo "Files:<br>";
//echo "<pre>"; var_dump($_FILES); echo "</pre>";

$target_path = "./flags/";

if($_FILES["zip_file"]["name"]) {
	$filename = $_FILES["zip_file"]["name"];
	$source = $_FILES["zip_file"]["tmp_name"];
	$type = $_FILES["zip_file"]["type"];
	
	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
	foreach($accepted_types as $mime_type) {
		if($mime_type == $type) {
			$okay = true;
			break;
		} 
	}
	
	$continue = strtolower($name[1]) == 'zip' ? true : false;
	if(!$continue) {
		$message = "Die hochzuladende Datei ist kein ZIP-File. Bitte noch einmal probieren.";
	} else {
  
  	$target_file = $target_path. $filename; 
  	echo "target_file = ". $target_file. "<br>";
  	
  	if(move_uploaded_file($source, $target_file)) {
  		$zip = new ZipArchive();
  		$x = $zip->open($target_file);
  		if ($x === true) {
  			$zip->extractTo($target_path); 
  			$zip->close();
  	
  			unlink($target_file);
  		}
  		$message = "Die Datei \"$filename\" wurde erfolgreich hochgeladen und entpackt.";
  	} else {	
  		$message = "Es gab ein Problem mit dem Upload. Bitte noch einmal probieren.";
  	}
	}
}

?>

  <div align="center">
    <?php if($message) echo "<p>$message</p>"; ?>
    <form enctype="multipart/form-data" method="post" action="">
      <label>
				Hier kannst Du alle Flaggen auf einmal hochladen.<br>
				Die Flaggen sollten als ZIP-Archiv von GIF-Bildern vorliegen, sonst werden sie nicht richtig erkannt.<br><br>
				W&auml;hle ein ZIP-File aus:<br><br>
				<input type="file" name="zip_file"/>
			</label>
      <br><br>
      <input type="submit" name="submit" value="Upload" />
    </form>
  </div>
</body>

</html>
