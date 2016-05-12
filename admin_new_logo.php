<?php 
/*****************************************************************************************************************************
** Hier kann der Admin ein GIF als Turnier-Logo hochladen - es wird ueberschriebn, falls schon vorhanden.
** File: "./flags/Logo.gif"
******************************************************************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Neues Turnierlogo"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php if(!isset($_POST['submit'])) { ?>
<form enctype="multipart/form-data" action="<?php $PHP_SELF ?>" method="post">
	<table width="600" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
		<tr>
			<td bgcolor="#e7e7e7" align="center" colspan="2"><b>Logo festlegen</b></td>
		</tr>
		<tr>
			<td>Logo</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="25000">
				<input type="file" name="datei">&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td bgcolor="#e7e7e7" align="center" colspan="2">
				<input type="submit" name="submit" value="Logo hochladen" class="button">
			</td>
		</tr>
	</table>
</form>
<?php

}else{

	/************************************
	** LOGO zum Hochladen gewaehlt ****
	*************************************/
	if(!empty($datei)) 
	{ 			
		// Original-Dateiname des Files verwerfen und die ID des neuen Teams als neuen Namen setzen
		$TID='Logo';
		$TID.='.gif';	
		$datei_name=$TID;
		
		print("<br>Soll gespeichert werden als: $datei_name<br>");
				
		$dateiname = $datei_name; 

	
		if($datei_size > $MAX_FILE_SIZE) 
		{ 
			echo "Die Datei ist zu groﬂ, die maximale Dateigr&ouml;sse betr‰gt $MAX_FILE_SIZE Byte(s)"; 
		} 
		else 
		{ 
			// Slash fuer Unix-Basis
			copy($datei,"flags/$dateiname"); 
			if( file_exists("flags/$dateiname")) 
			{ 
				echo "<br>Die Datei <b>$datei_name</b> wurde mit <b>$datei_size Byte</b> erfolgreich hochgeladen"; 
			} 
			// Wenn Datei nicht in Unix-FileSystem geladen werden konnte
			elseif(! file_exists("flags/$dateiname")) 
			{ 
				// Probiere mit Backslash auf Windows-Basis zu laden
				copy($datei,"flags\\$dateiname"); 
				if( file_exists("flags\\$dateiname")) 
				{ 
					echo "<br>Die Datei <b>$datei_name</b> wurde mit <b>$datei_size Byte</b> erfolgreich hochgeladen"; 
				} 
				elseif(! file_exists("flags\\$dateiname")) 
				{ 
					echo "<br>Fehler beim Datei-Upload<br>"; 
				} 
			}
					


		} 	}  //End Of if(!empty...
	/*****  END OF   ********************
	** Flagge zum hochladen gewaehlt ****
	*************************************/
	//WENN datei == empty dann koennte es auch daran liegen, dass PHP5 eingesetzt wird, also checken:
	// Pr¸fen des Array $_FILES
	elseif(isset($_FILES["datei"]))
	{
		/*******************************   PHP 5 checker **************************************************/
		// Upload-Status
		if ($_FILES["datei"]["error"] == UPLOAD_ERR_OK) 
		{
			// Muster zur ‹berpr¸fung der im Dateinamen
			// enthaltenen Zeichen (Optional)
			$regExp = "/^[a-z_]([a-z0-9_-]*\.?[a-z0-9_-])*\.[a-z]{3,4}$/i";
			
			// Dateiname und Dateigrˆsse
			if (preg_match($regExp,$_FILES["datei"]["name"]) && $_FILES["datei"]["size"] > 0 && $_FILES["datei"]["size"] < 100000) 
			{
				
				// Tempor‰re Datei in das Zielverzeichnis
				// des Servers verschieben.
				$datei = $_POST[datei];
				$dateiname = "flags/Logo.gif";
				if (file_exists($dateiname))
				 @unlink($dateiname);  
			
				move_uploaded_file($_FILES["datei"]["tmp_name"],$dateiname);
				
				// Erfolgs-Meldung
				print("Datei erfolgreich hochgeladen!<br>");
			}
			else 
			{
				echo "Fehler: Im Dateinamen oder Dateigrˆssen Limit!";
			}
		}
		else 
		{
			echo "Fehler: W‰hrend der ‹bertragung aufgetreten!";
		}
	/****************************  Ende    PHP 5 checker   Ende  **********************************************/
	}
	//weder php4 noch php5 kann ne hochgeladene datei finden
	else
	{
		echo "Fehler: Dateiupload fehlgeschlagen!";
	}
	
} //End of CLICKED 

?>
</body>
</html>
