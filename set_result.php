<?php 
/*********************************************************************************
** Die Datei holt alle Spiele, die nicht in der Zukunft (Datum) liegen		**
** und bei denen bei Tore2 noch -1 eingetragen ist.				                **
** Hier soll kann dann das tatsaechliche Ergebnis eingetragen werden 		  **
** Selbstverstaendlich hat nur "admin" Zufriff auf diese Seite			      **
**********************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));

// aktuelle Zeit holen
$heute   = today();
$uhrzeit = now();

// 'submit' gedrückt?
$submit  =  $_POST["submit"];

//print "heute = $heute<br>";
//print "jetzt = $uhrzeit<br>";
//print "submit = \"$submit\"<br>";

/*
Anfrage zur Ermittling der zu vergebenen Punkte
*/
$dbanfrage = "SELECT Punkte_korrekter_Tipp, Punkte_korrekte_Tore, Punkte_korrekter_Sieger
							FROM settings ";                
$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
$ausgabe = mysql_fetch_array ($result);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Spielergebnis eingeben"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="set_result.php" method="post">
<div align="center">
<?php

//Hole Spiele_IDs die NICHT in der Zukunft liegen UND Tore2 -1 ist(also noch kein Ergebnis eingetragen ist)
//$db_get_games="SELECT SPIEL_ID
//               FROM spiel  WHERE Tore2=-1  AND DATE_FORMAT( Datum,  '%Y-%m-%d'  )  <= DATE_ADD( CURDATE(  ) ,  INTERVAL  -0 DAY  )";
$db_get_games="SELECT SPIEL_ID
               FROM spiel  
               WHERE Tore2=-1 AND DATE_FORMAT(Datum, '%Y-%m-%d') <= \"$heute\"";
$ggID_result = mysql_query($db_get_games);

//Gehe Spiel-IDs duch, die gerade geholt wurden

//Wenn EINTRAGEN geklickt wurde
if ($submit == " Eintragen ") {

		if (debug()) {
			print "=====================================================================================================<br>";
		}

    while ($ausgabe_get_gameID = mysql_fetch_array ($ggID_result))
    {
        /**********************
        saving Data
        ***********************/
        $tor1 = $_POST["S$ausgabe_get_gameID[SPIEL_ID]_Team1"];
        $tor2 = $_POST["S$ausgabe_get_gameID[SPIEL_ID]_Team2"];
				
        //Spiel aktualisieren-Befehl
        $gameUpdate = "UPDATE spiel SET Tore1=$tor1, Tore2=$tor2 WHERE SPIEL_ID=$ausgabe_get_gameID[SPIEL_ID]";
        mysql_query($gameUpdate);

        /*++++++++++++++++++++++++++++++++++++++++++++++++
        Punkte bei den Tipps zu diesem Spiel eintragen
        +++++++++++++++++++++++++++++++++++++++++++++++++*/

        $db_get_tipps="SELECT t.Tore1, t.Tore2, t.Faktor, t.USER_ID, u.user FROM tipp t, user u WHERE u.USER_ID = t.USER_ID AND SPIEL_ID=$ausgabe_get_gameID[SPIEL_ID]";
        $get_tipps_result = mysql_query($db_get_tipps);
        while ($ausgabe_get_tipp = mysql_fetch_array ($get_tipps_result))
        {
				
            if (debug()) {
	   					print "tor1 : tor2 = ". $tor1. " : ". $tor2. "<br>";
					    print "Tipp von user ". $ausgabe_get_tipp[User]. ":  tor1 : tor2 (Faktor) = ". $ausgabe_get_tipp[Tore1] ." : ". $ausgabe_get_tipp[Tore2]. 
						        "(". $ausgabe_get_tipp[Faktor]. ")<br>";
					  }
				
            if ($ausgabe_get_tipp[Tore1] == 99 || $ausgabe_get_tipp[Tore2] == 99) { // nicht getippt
              $points = 0; 
            } else if(($ausgabe_get_tipp[Tore1] == $tor1)  && ($ausgabe_get_tipp[Tore2] == $tor2)) { // Tipp korrekt
                $points=$ausgabe[Punkte_korrekter_Tipp];//$points=3;
            } else if (($tor1 - $ausgabe_get_tipp[Tore1]) == ($tor2 - $ausgabe_get_tipp[Tore2]) && $tor1 > -1) { // nur korrekte Differenz
	            $points=$ausgabe[Punkte_korrekte_Tore];//$points=2;
            } else if (($tor1 > $tor2 && $ausgabe_get_tipp[Tore1] > $ausgabe_get_tipp[Tore2]) || ($tor1 < $tor2 && $ausgabe_get_tipp[Tore1] < $ausgabe_get_tipp[Tore2])) { // nur korrekter Sieger
                $points=$ausgabe[Punkte_korrekter_Sieger];//$points =1;
            } else { // alles falsch getippt
              $points = 0;
            }

            if (debug()) {
              print "Ergebnis: $points Punkte<br>";
						}

            //errechnete points in Tipp speichern            
            $Tipp_Points_Update = "UPDATE tipp SET TippPunkte=$points WHERE SPIEL_ID=$ausgabe_get_gameID[SPIEL_ID] AND USER_ID=$ausgabe_get_tipp[USER_ID]";
            mysql_query($Tipp_Points_Update);

            //Tipp-Punkte * Faktor = Spielpunkte !!!
            $Game_Points=$points * $ausgabe_get_tipp[Faktor];
            $Game_Points_Update = "UPDATE tipp SET SpielPunkte=$Game_Points WHERE SPIEL_ID=$ausgabe_get_gameID[SPIEL_ID] AND USER_ID=$ausgabe_get_tipp[USER_ID]";
            mysql_query($Game_Points_Update);
                       
            /************************************************************************************
            Die Summe der Spielpunkte plus MeisterTippPunkte in Gesamtpunkte des Users speichern
            *************************************************************************************/
                
            //TotalPoints werden BERECHNET (Summe der SpielPunkteSpalte des aktuellen users !//
            $calc_sum=("SELECT SUM(SpielPunkte) as total FROM tipp WHERE USER_ID=$ausgabe_get_tipp[USER_ID]");
            $calc_pointsum=mysql_query($calc_sum);
            $data = mysql_fetch_array($calc_pointsum);
            $totalpoints =  $data[total];

            if (debug()) {
              print "totalpoints = $totalpoints<br>";
						}
            
            //MeisterTippPunkte holen und draufaddieren (waehrend dem Turnier immer = 0)
            $MeisterPoints=("SELECT MeisterTippPunkte FROM user WHERE USER_ID=$ausgabe_get_tipp[USER_ID]");
            $get_MeisterPoints=mysql_query($MeisterPoints);
            $data = mysql_fetch_array($get_MeisterPoints);
            
            $totalpoints = $totalpoints + $data[MeisterTippPunkte];
            if (debug()) {
              print "totalpoints (inkl. Meistertipp) = $totalpoints<br>";
							print "=====================================================================================================<br>";
						}
                        
            // neue Variante: Gesamtpunkte immer neu SETTEN//                
            $Total_Points_Update = "UPDATE user SET TotalPoints=$totalpoints WHERE USER_ID=$ausgabe_get_tipp[USER_ID]";
            mysql_query($Total_Points_Update);

        }   //End of while ($ausgabe_get_tipp ...


        }
    $submit =0;
        
    print('
    <table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">
      <tr>
        <td bgcolor="#e7e7e7" align="center">
          <br>
          "<b>Ergebnisse wurden gespeichert"</b>"
          <br><br>
        </td>
      </tr>
    </table>');


}
/*****************************************
** 	Kein SUBMIT gedrueckt 		**
*****************************************/
else{

    while ($ausgabe_get_gameID = mysql_fetch_array($ggID_result))
	
	
    {


        //Hole alle Infos zu dem Spiel, das gerade ausgegeben werden soll
     $dbanfrage = "SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2
        FROM spiel s, team t1, team t2
        WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.SPIEL_ID = $ausgabe_get_gameID[SPIEL_ID]";
        $result_anfrage=  mysql_db_query ($dbName, $dbanfrage, $connect);
        $ausgabe_anfrage = mysql_fetch_array ($result_anfrage);

	/********************************
	Spaeteste Tippabgabezeit <-----
	*******************************/
	$spieldate = $ausgabe_anfrage[Datum];
	$spieltime = $ausgabe_anfrage[Anpfiff];

	print("<br>Spiel_ID: $ausgabe_get_gameID[SPIEL_ID] - Datum: $ausgabe_anfrage[Datum] - Anpfiff: $ausgabe_anfrage[Anpfiff]<br>");
	print("<br>$ausgabe_anfrage[Name] ");

	/*****************************************
	** Wenn schon angepfiffen: show DropDown *
	******************************************/
	if (($heute > $spieldate) || ($heute == $spieldate && $uhrzeit > $spieltime))
	{
        print ("<SELECT NAME='S$ausgabe_get_gameID[SPIEL_ID]_Team1'>");
        /****************
        Team1 - Drop-Down
        *****************/
        for ($index=-1;$index<10;$index++) {
            if ($index == -1) {
            //Kein Eintrag
            print ("<OPTION selected VALUE='-1'> ");
        } else {
            print ("<OPTION VALUE='$index'>$index");
        }
    }
    print ("</SELECT>");
    
    print (" : ");
    
    print ("<SELECT NAME='S$ausgabe_get_gameID[SPIEL_ID]_Team2'>");
    /****************
    Team2 - Drop-Down
    *****************/
    for ($index=-1;$index<10;$index++) {
        if ($index == -1) {
            //Kein Eintrag
            print ("<OPTION selected VALUE='-1'> ");
        } else {
            print ("<OPTION VALUE='$index'>$index");
        }
    }
    print ("</SELECT>");
        
    print(" $ausgabe_anfrage[Name2]<br>");

   } // ************ END OF: WENN SCHON ANGEPFIFFEN: Show DropDown *********************

   else
   {
	print(" : $ausgabe_anfrage[Name2]<br><br><b>Info: Die Ergebniseingabe wird nach Spielbeginn freigeschaltet!</b><br><br>");
   }

    print("<br><hr><br>");
}

}

if ($submit != " Eintragen ")
{
    if (mysql_num_rows($ggID_result) > 0) {
      print('<input type="submit" name="submit" value=" Eintragen ">');
    } else {
      print("Derzeit gibt es keine Spiele einzutragen. Du musst warten, bis das nächste Spiel fertig ist!<br>");
    }
}

?>
</div>
</form>


</body>
</html>

    