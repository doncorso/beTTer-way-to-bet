<?php
/*
** Hier kann der Admin neue Teams anlegen ... 
*/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Team anlegen"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<?php
/*************************************************
** Checkt, ob Datei, die upgeloaded				**
** werden soll, schon vorhanden ist,			**
** um Ueberschreiben zu Verhinden, wird			**
** der neuen Datei copy_of VORANgestallt		**
** und geprueft, ob dieser Name schon vor-		**
** handen ist USW...							**
*************************************************/
function check_datei() 
{ 
global $datei_name, $dateiname; 
    $backupstring = "copy_of_"; 
    $dateiname = $backupstring."$dateiname"; 

    if( file_exists($dateiname)) 
    { 
        check_datei(); 
    } 
} 

?>

<?php if(!isset($_POST['submit'])) { ?>
<form enctype="multipart/form-data" action="<?php $PHP_SELF ?>" method="post">
	<table width="600" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<b>Neues Team anlegen</b>
		</td>
		</tr>
		<tr>
			<td width="170" bgcolor="#e7e7e7">Name des Teams</td>
			<td width="230" bgcolor="#ffffff"><input type="text" name="name" class="input" size="20"></td>
		</tr>
		<tr>
		<td>
		Flagge
		</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="5000">
			<input type="file" name="datei">&nbsp;&nbsp;&nbsp;
		</td>
		</tr>
		<tr>
		<td bgcolor="#e7e7e7" align="center" colspan="2">
		<input type="submit" name="submit" value="Team anlegen" class="button">
		</td>
		</tr>
	</table>
</form>
<?php

}else{

	/** TEAM ANLEGEN geklickt  **/	
	$name = $_POST['name'];
	
	$dbanfrage	 = "SELECT COUNT(*)
					FROM team WHERE Name='$name'";
	$result		  = mysql_db_query ($dbName, $dbanfrage, $connect);
	$ausgabe	  = mysql_fetch_array ($result);	
	$Anzahl_Teams = $ausgabe[0];
	
	if ($Anzahl_Teams != 0)
	{
		print("<br>Dieser Teamname ist schon vorhanden. <br>
 		Zwei Teams mit demselben Namen w&uuml;rden einen eindeutigen Tipp unm&ouml;glich machen ...<br><br>");
	}
	
	/***
	** Wenn es noch kein Team mit diesem Namen gibt: anlegen !!!
	*****/
	else{
		
		if($insert = @mysql_query("INSERT INTO team SET Name = '$name'")) {
			echo '<p align="center">Das neue Team wurde erfolgreich angelegt!<br><br>';
		} else	{
			echo '<p align="center">Beim Anlegen des neuen Teams trat leider ein Fehler auf!<br><br>';
		}	
	
		/************************************
		** Flagge zum hochladen gewaehlt ****
		*************************************/
		if(!empty($datei)) 
		{ 
		
			$dbanfrage_new_team = "SELECT TEAM_ID
									FROM team
									WHERE Name = '$name'";              
			$result_ID = mysql_db_query ($dbName, $dbanfrage_new_team, $connect);
			$new_ID = mysql_fetch_array ($result_ID);
			
			
			// Original-Dateiname des Files verwerfen und die ID des neuen Teams als neuen Namen setzen
			$TID=$new_ID[TEAM_ID];
			$TID.='.gif';	
			$datei_name=$TID;
			
			print("<br>Neuer Name: $datei_name<br>");
					
			$dateiname = $datei_name; 
		/**** WENN DATEI NICHT UEBERSCHRIEBEN WERDEN SOLL, BENENNT check_Datei() DIE DATEI UM In "COPY_OF..." - HIER NICHT SINNVOLL
			if( file_exists($datei_name)) 
			{ 
				check_datei(); 
				echo "Die Datei mit dem Dateinamen <b>$datei_name</b> existierte bereits.<br> Ihre Datei wurde in <b>$dateiname</b> umbenannt"; 
			}
		*****/ 
		
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
						
	
	
			} 
		}  //End Of if(!empty...
		/*****  END OF   ********************
		** Flagge zum hochladen gewaehlt ****
		*************************************/
	} // End of IF TEAMNAME NOCH NICHT VORANDEN	
	
		
} //End of CLICKED 

?>
</body>
</html>
