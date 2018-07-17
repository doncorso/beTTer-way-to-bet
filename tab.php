<?php
/* Meine Tipps */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array(), array("Gast", "admin"));

// Zur Vereinfachung
$user = $_SESSION['user'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Meine Tipps"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="tab.php" method="post">

<?php
require("connect.inc.php");

/*****************************************************************
 Es folgt allerlei Berechnungskram, der sp�ter verwendet wird...
 ******************************************************************/

// Heutiges Datum und Uhrzeit auslesen (f�r Abgleich: Spiel(e) in der Zukunft / Vergangenheit)
$heute = today();
$time  = now();

// ID und maximale Restpunkte vom Benutzer auslesen
$db_getUserID = "SELECT u.USER_ID, u.TotalPoints, u.Konto FROM user u WHERE  user=\"$user\"";
$result1 = mysql_db_query ($dbName, $db_getUserID, $connect);
$ausgabeID = mysql_fetch_array ($result1);
$userID = $ausgabeID[0];
$userKonto = $ausgabeID[Konto];

// Filterung initial auf "die n�chsten 3" stellen
$chosen_kat="103";
	
// Uebergebener Parameter, der Filterung festlegt
// "%" = keine Filterung (zeige alle), ansonsten sind nur Werte > 0 legal
if ($_POST[kat] > 0 || $_POST[kat] == "%") $chosen_kat = $_POST[kat];



// kat_flag ist das eigentliche Kategorie-Flag, das beim Datenbank-Zugriff benutzt wird
$kat_flag = $chosen_kat;
if ($chosen_kat >= 100) { // values >= 100 sind spezielle Optionen wie z.B. "Die n�chsten x Spiele", ergo: nimm alle Kategorien
  $kat_flag = "%";
}

if (debug()) {
  print "chosen_kat = $chosen_kat<br>";
	print "kat_flag   = $kat_flag<br>";
}

// Summe Gesetzter Faktorpunkte Kategorie
$SGFK = 0;
$calc_SGFK=("SELECT SUM(t.Faktor) as katfats FROM tipp t, spiel s WHERE t.USER_ID=$userID AND t.SPIEL_ID=s.SPIEL_ID AND s.Kategorie LIKE '$kat_flag'");
$res_SGFK = mysql_db_query($dbName,$calc_SGFK, $connect); 
$SGFK_out = mysql_fetch_array($res_SGFK); 
$SGFK =  $SGFK_out[katfats];

// Summe Gesetzter Faktorpunkte Kategorie in der VERGANGENHEIT (nicht mehr �nderbar !!!)
$SGFKV = 0;
$calc_SGFKV=("SELECT SUM(t.Faktor) as katfatsverg FROM tipp t, spiel s WHERE t.USER_ID=$userID AND t.SPIEL_ID=s.SPIEL_ID AND s.Kategorie LIKE '$kat_flag' AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff < \"$time\"))");

$res_SGFKV = mysql_db_query($dbName,$calc_SGFKV, $connect); 
$SGFKV_out = mysql_fetch_array($res_SGFKV); 
$SGFKV =  $SGFKV_out[katfatsverg];

if (debug()) { 
	print("<br> calc_SGFKV: $calc_SGFKV <br>");
}

// TotalPoints werden BERECHNET (Summe der SpielPunkteSpalte des aktuellen users !                                                                               
$calc_sum=("SELECT SUM(SpielPunkte) as total FROM tipp WHERE USER_ID=$userID");
$calc_pointsum=mysql_db_query($dbName,$calc_sum, $connect); 
$data = mysql_fetch_array($calc_pointsum); 
$totalpoints =  $data[total];
if ($totalpoints =="") $totalpoints = 0;

//Bisher gesetzte Faktorpunkte des users holen
$calc_sumfak=("SELECT SUM(Faktor) as totalF FROM tipp WHERE USER_ID=$userID");
$calc_faksum=mysql_db_query($dbName,$calc_sumfak, $connect); 
$faksum = mysql_fetch_array($calc_faksum); 
$SummeFaktor =  $faksum[totalF];

//Holt Maximal zusaetzlich vergebbare Faktorpunkte
$db_getFaktor = "SELECT Konto FROM `user` WHERE USER_ID=$userID";
$resultF = mysql_query($db_getFaktor);
$ausgabeF = mysql_fetch_array ($resultF);
$faktorMax = $ausgabeF[0];

//Bisher gesetzte Faktorpunkte des users holen
$calc_sumfak=("SELECT SUM(Faktor) as totalF FROM tipp WHERE USER_ID=$userID");
$calc_faksum=mysql_db_query($dbName,$calc_sumfak, $connect); 
$faksum = mysql_fetch_array($calc_faksum); 
$SummeFaktor =  $faksum[totalF];

//Holt Anzahl Spiele (=minimal vergebene Faktorpunkte)
$db_getAnzSpiele = "SELECT COUNT(*) FROM `spiel`";
$resultA = mysql_query($db_getAnzSpiele);
$ausgabeA = mysql_fetch_array ($resultA);
$faktorMin = $ausgabeA[0];

//Holt Anzahl Spiele (=minimal vergebene Faktorpunkte)
$db_getAnzRestSpiele = "SELECT COUNT(*) FROM `spiel` WHERE Datum > \"$heute\" OR (Datum = \"$heute\" AND Anpfiff > \"$time\") ";
$resultB = mysql_query($db_getAnzRestSpiele);
$ausgabeB = mysql_fetch_array ($resultB);
$anzRestSpiele = $ausgabeB[0];

//print("<br><hr><br> FaktorMax= $faktorMax <br> SummeFaktor = $SummeFaktor <br><br><hr><br>");

// Hat user schon einmal getippt?
$db_anzTipps = "SELECT COUNT(*) FROM `tipp` WHERE USER_ID=$userID";
$resultA2 = mysql_query($db_anzTipps);
$ausgabeA2 = mysql_fetch_array ($resultA2);
$firstTip = ($ausgabeA2[0] == 0);

/**********************************************************************************
Pr�fen, ob bereits Spiele gelaufen sind und die Anzahl Faktorpunkte abziehen 
(d.h. nicht getippt = Faktor 1)
**********************************************************************************/  

//// Hole alle Spiele der Vergangenheit, die vom User getippt wurden
//$db_getippt = " 
//  SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2, t.USER_ID as USER_ID
//	FROM spiel s, team t1, team t2, tipp t
//	WHERE s.Team1 = t1.TEAM_ID 
//	  AND s.Team2 = t2.TEAM_ID 
//	  AND s.SPIEL_ID = t.SPIEL_ID
//	  AND t.USER_ID = $userID 
//	  AND s.Kategorie LIKE '%' 
//	  AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff <= \"$time\"))
//	ORDER BY s.Datum ASC, s.Anpfiff ASC";
//                  		
//$result_getippt = mysql_query($db_getippt);
//$num_getippt = mysql_num_rows($result_getippt);
//
//$db_letzte_spiele = " SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
//                  		FROM spiel s, team t1, team t2
//                  		WHERE s.Team1 = t1.TEAM_ID 
//                  		  AND s.Team2 = t2.TEAM_ID 
//                  		  AND s.Kategorie LIKE '%' 
//                  		  AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff <= \"$time\"))
//                  		ORDER BY s.Datum ASC, s.Anpfiff ASC";
//                  		
//$result_letzte_spiele = mysql_query($db_letzte_spiele);
//$num_letzte_spiele = mysql_num_rows($result_letzte_spiele);
//
//if (debug()) {
//  print "firstTip          = $firstTip<br>";
//  print "num_getippt       = $num_getippt<br>";
//	print "num_letzte_spiele = $num_letzte_spiele<br>";
//}
//
//// Wenn nicht alle vergangenen Spiele getippt, dann fehlende Spiele eintragen
//if ($num_getippt < $num_letzte_spiele) {
//  while ($row_letzte_spiele = mysql_fetch_assoc ($result_letzte_spiele)) {
//
//   //echo "row_letzte_spiele:<br>";
//   //dump($row_letzte_spiele);
//
//    $found = false;
//    while ($row_getippt = mysql_fetch_assoc ($result_getippt)) {
//
//      //echo "row_getippt:<br>";
//      //dump($row_getippt);
//   
//      if ($row_getippt[SPIEL_ID] == $row_letzte_spiele[SPIEL_ID]) {
//        $found = true;
//        break;
//      }
//    }
//    
//    if (!$found) {
//      echo "row_letzte_spiele:<br>";
//      dump($row_letzte_spiele);
//
//      print "Adding forgotten tipp for game ". $row_letzte_spiele[SPIEL_ID]. " and user $userID into tipps db...<br>";
//      //FIXME!!! Add tipp with Tore1/2 = -1, TippPunkte = 0, Faktor = 1, Spielpunkte = 0!!!
//    }
//
//    // internen Datenzeiger wieder auf Anfang von getippt setzen, sonst: n�chster Schleifendurchlauf falsch    
//		if ($num_getippt > 0) {
//			mysql_data_seek($result_getippt, 0);
//		}
//  }
//}
//
//// internen Datenzeiger wieder auf Anfang von letzte_spiele setzen, damit sp�ter keine Verwirrungen passieren
//if ($num_letzte_spiele > 0) {
//	mysql_data_seek($result_letzte_spiele, 0);
//}


/*****************************************************************
 Ende Berechnungskram, der sp�ter verwendet wird...
 ******************************************************************/

if (isset($_POST[submit])) {
	/*
	 * SUBMIT wurde gedrueckt und Seite neu geladen
	 */

  //falsch: $SGFK = $_POST[SGFK];
  print("<table width=\"50%\" bgcolor=\"#000000\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" align=\"center\">"); 
  print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">
         Danke f&uuml;rs Tippen, $user. <br>Nachdem Deine Angaben &uuml;berpr&uuml;ft werden, werden sie gespeichert.<br>");
  print("Zur Zeit hast Du $totalpoints Punkte - Viel Gl&uuml;ck!<br></th>");
  print("</table>");
  print("<br><hr><br><br>");

} else if (!isset($_POST[submit])) {

	/*
	 * SUBMIT wurde noch nicht geklickt
	 */ 
  print("<table width=\"50%\" bgcolor=\"#000000\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" align=\"center\">");
  print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">
         Deine Punkte: $totalpoints<br></th>");
  print("</table>");
  print("<br><hr><br><br>");
}

/**********************************************************/

	/*********************************************************
	** DropDown mit allen verfuegbaren Kategorien zur	**
	** Auswahl der Filterung der Anzeige der Spiele		**
	**********************************************************/
        
	/*************************
	** Baue DropDown-Box auf**
	**************************/ ?>
	<p align="center">
	Folgende Spiele anzeigen: 
	<select name="kat" id="kat">
	<?php
	/*************************
	** Hole alle KATEGORIEN **
	**************************/
	$db_get_katos = "SELECT * FROM kategorie";
	$result_katos = mysql_db_query($dbName, $db_get_katos, $connect);
		
		/*************************
		**  F�lle DropDown-Box  **
		**************************/
		// Extra-Optionen (Annahme: wir werden nie mehr als 100 Kategorien haben)
		print("<option value=103");
	 	if($chosen_kat=="103") print(" selected");
   	print(">Die n&auml;chsten 3</option>");

		print("<option value=104");
	 	if($chosen_kat=="104") print(" selected");
   	print(">Die n&auml;chsten 4</option>");

		print("<option value=105");
	 	if($chosen_kat=="105") print(" selected");
   	print(">Die n&auml;chsten 5</option>");

		print("<option value=110");
	 	if($chosen_kat=="110") print(" selected");
   	print(">Die n&auml;chsten 10</option>");

		print("<option value=203");
	 	if($chosen_kat=="203") print(" selected");
   	print(">Die letzten 3</option>");

		print("<option value=204");
	 	if($chosen_kat=="204") print(" selected");
   	print(">Die letzten 4</option>");

		print("<option value=205");
	 	if($chosen_kat=="205") print(" selected");
   	print(">Die letzten 5</option>");

		print("<option value=210");
	 	if($chosen_kat=="210") print(" selected");
   	print(">Die letzten 10</option>");

    // Reine Kategorien hinzuf�gen 
		while($katos_ausgabe = mysql_fetch_array($result_katos))
		{
			print("<option value=\"$katos_ausgabe[KATEGORIE_ID]\"");
   		if($katos_ausgabe[KATEGORIE_ID] == $chosen_kat) print(" selected");
  	  print(">$katos_ausgabe[Kategoriename]</option>");
		}
		
		print("<option value=%");
		if($chosen_kat=="%") print(" selected");
 	 	print(">Alle</option>");
	?>
  </select>
	&nbsp &nbsp &nbsp

	<?php /* Button zur Aktivierung der Filterung */	?>
	<input type="submit" name="filter" value=" GO! ">
	</p>

	<br><hr><br>
	<?php


/**********************************************************/


	/*********************************************************
	** 	Datenbankanfrage, die die Spiele holt, die 	**
	** 		zur gew�hlten Kategorie geh�ren 	**
	**********************************************************/

  // Suche die Spiele der passenden Kategorie
	$dbanfrage = "	SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie LIKE '$kat_flag'
		ORDER BY s.Datum ASC, s.Anpfiff ASC";
		
	if ($chosen_kat >= 200) { // = "letzte n Events", muss andere Abfrage ergeben

  // Suche die letzten n Spiele. Logik: $chosen_kat enth�lt bereits die Anzahl zu suchender Spiele, man muss nur 200 abziehen.
	// Grund: Da $chosen_kat eine Zahl zur�ckgibt, die der ausgew�hlten Kategorie entspricht, musste hierf�r zuvor 200 hinzuaddiert werden,
	// damit die Auswahl eindeutig bleibt.
	$dbanfrage = "  SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie LIKE '%' AND (s.Datum < \"$heute\" OR (s.Datum = \"$heute\" AND s.Anpfiff <= \"$time\"))
		ORDER BY s.Datum DESC, s.Anpfiff DESC
    LIMIT 0,". ($chosen_kat - 200);

	} else if ($chosen_kat >= 100) { // = "n�chste n Events", muss andere Abfrage ergeben

  // Suche die n�chsten n Spiele. Logik: $chosen_kat enth�lt bereits die Anzahl zu suchender Spiele, man muss nur 100 abziehen.
	// Grund: Da $chosen_kat eine Zahl zur�ckgibt, die der ausgew�hlten Kategorie entspricht, musste hierf�r zuvor 100 hinzuaddiert werden,
	// damit die Auswahl eindeutig bleibt.
	$dbanfrage = "  SELECT s.Datum, s.Anpfiff, t1.Name, t2.Name as Name2, s.Tore1, s.Tore2, s.SPIEL_ID, s.Team1, s.Team2, t1.TEAM_ID as TEAM_ID1, t2.TEAM_ID as TEAM_ID2
		FROM spiel s, team t1, team t2
		WHERE s.Team1 = t1.TEAM_ID AND s.Team2 = t2.TEAM_ID AND s.Kategorie LIKE '%' AND (s.Datum > \"$heute\" OR s.Datum = \"$heute\" AND s.Anpfiff > \"$time\")
		ORDER BY s.Datum ASC, s.Anpfiff ASC
    LIMIT 0,". ($chosen_kat - 100);
	}
	
 	$result = mysql_db_query ($dbName, $dbanfrage, $connect);


	//wenn auf absenden geklickt, zaehle insgesamt neu zu vergebene Faktorpunkte um auszuschliessen, dass gesamt gesetzte FPs - vorher zu dieser Kategorie gesetzte FPs + JETZT zu dieser kategeorie zu setzende FPs GR�?ER WIRD, als GESAMT m�glich sein soll (=KONTO)
	if (isset($_POST[submit]))
	{
		//Insgesamt vom User gesetzte Faktorpunkte holen
		$calc_sumfak=("SELECT SUM(Faktor) as totalF FROM tipp WHERE USER_ID=$userID");
		$calc_faksum=mysql_db_query($dbName,$calc_sumfak, $connect); 
		$faksum = mysql_fetch_array($calc_faksum); 
		$SGGF =  $faksum[totalF];
		
		//SUMME NEU VERTEILTE FAKTORPUNKTE (noch nicht gespeichert, nur wenn nicht zu viele FPs vergeben wurden, DARF gespeichert werden
		$SNVF=0;
		while ($ausgabe = mysql_fetch_array ($result))
		{
			$newFaktor = $_POST["F$ausgabe[SPIEL_ID]"];
			$SNVF += $newFaktor;
		}
		
		//array "zuruecksetzen", damit naechstes while wieder den ganzen array durchlaufen kann
		$result = mysql_db_query ($dbName, $dbanfrage, $connect);
		
		//$SGFKV kennt die Summe gesetzter FPs der gewaehlten Kategorie und Spielen in der Vergangenheit
		
		/*****************
		** TESTAUSGABE	**
		*****************/
		if (debug())
		{
			print("<br><br><hr><br>
				Summe gesamt gesetzte Faktorpunkte: $SGGF <br>
				Summe gesamte Faktorpunkte der Kategorie <strong>$chosen_kat</strong>: $SGFK <br>
				Summe der nicht mehr zu &auml;ndernden FPs dieser Kategorie weil Spiel in der Vergangenheit:$SGFKV <br>
				Summe neu verteilte Faktorpunkte: $SNVF <br>
				Gesamt verggebbare FaktorPunkte: $userKonto <br>
				Durchschnittl. Faktorpunkte pro Spiel: $avgFPS
				");
		}
			$zuvielefaktorpunktevergeben = false;
			
			$differenz = $SGGF - $SGFK + $SNVF + $SGFKV;
			if($differenz > $userKonto)
			{
				$differenz -= $userKonto;
        $text  = "<br>Es "; 
  			$text .= singplur("wurde", "wurden", $differenz) . " ";
				$text .= singplur("EIN", $differenz, $differenz) . " ";
				$text .= singplur("Faktorpunkt", "Faktorpunkte", $differenz);
				$text .= " zuviel gesetzt. Bitte korrigieren!<br>";
				print($text);
				$zuvielefaktorpunktevergeben = true;
			}
			
			//ob gespeichert wurde oder nicht, muss per POST uebergeben werden! (wg. Berechnung)
			echo'<input type="hidden" name="zuvielefaktorpunktevergeben" value="'.$zuvielefaktorpunktevergeben.'">';
			
			print("<br><br><hr><br>");
	}



//Solange ein Spiel aus der Datenbank geholt werden kann
while ($ausgabe = mysql_fetch_array ($result))
{
   
	/*********************************************************
	 Wenn noch getippt werden darf (Spiel ist in der Zukunft)
	 **********************************************************/
     
	if (($ausgabe[Datum] > $heute) || (($ausgabe[Datum] == $heute) && ($ausgabe[Anpfiff] > $time))) {   

		/*********************************************************************************************************************
		**********************************************************************************************************************
		**********************************************************************************************************************
		**                             Wenn Submit gedrueckt wurde: Tipps speichern					    **
		**********************************************************************************************************************
		**********************************************************************************************************************
		*********************************************************************************************************************/
	
	
		if (isset($_POST[submit]) && $zuvielefaktorpunktevergeben == true) 
		{
			$text = singplur("EINEN", $differenz, $differenz) . " ". singplur("FAKTORPUNKT", "FAKTORPUNKTE", $differenz);
			print("DER TIPP WURDE NOCH NICHT GESPEICHERT! BITTE VERGIB INSGESAMT <strong>$text</strong> WENIGER!!<br>");
		}
		//wenn submit UND nicht zuviele Faktors vergeben: SPEICHERN!
		elseif (isset($_POST[submit]) && !$zuvielefaktorpunktevergeben) 
		{
			// saving Data
			$tor1 = $_POST["S$ausgabe[SPIEL_ID]_Team1"];
			$tor2 = $_POST["S$ausgabe[SPIEL_ID]_Team2"];
	
			if (debug()) {
				print "tor1 = $tor1<br>";
				print "tor2 = $tor2<br>";
			}
		 
 		  // sicherstellen, dass die Anzahl Tore g�ltig ist
		  $tor1 = correctTor($tor1, MIN_TORE, MAX_TORE);
		  $tor2 = correctTor($tor2, MIN_TORE, MAX_TORE);		 

 		  if (debug()) {
			  print "tor1 = $tor1<br>";
			  print "tor2 = $tor2<br>";
		  }
		 
			//newFaktor aus Uebergabe-String holen
			$newFaktor = $_POST["F$ausgabe[SPIEL_ID]"];
			if ($newFaktor < 1) {
				 $newFaktor = 1;
			}
			if ($newFaktor > 5) {
				 $newFaktor = 5;
			}
			
			//Faktor uebernehmen
			$aktuellFaktor = $newFaktor;
			
			//Tipp speichern-Befehl
			$tippInsert = "INSERT INTO tipp SET USER_ID=$userID, SPIEL_ID=$ausgabe[SPIEL_ID], Tore1=$tor1, Tore2=$tor2, Faktor=$newFaktor";
			//Tipp aktualisieren-Befehl
			$tippUpdate = "UPDATE tipp SET  Tore1=$tor1, Tore2=$tor2, Faktor=$newFaktor WHERE USER_ID=$userID AND SPIEL_ID=$ausgabe[SPIEL_ID]";
			
			mysql_query($tippUpdate);
			//Wenn Update keine Zeile betrifft
			if (!mysql_affected_rows()==1){
				//dann Insert
				mysql_query ($tippInsert) ;
			}
		} //End of "if (isset($_POST[submit]) && $zuvielefaktorpunktevergeben == ...)"
         
         
		//AUSGABE //
             
             
     $tor1 = 0;
     $tor2 = 0;
     //StandardFaktor
     $aktuellFaktor = 1;

     $getTipp = "SELECT t.Tore1, t.Tore2, t.Faktor FROM tipp t WHERE USER_ID=$userID AND SPIEL_ID=$ausgabe[SPIEL_ID]";
     $result3 = mysql_query($getTipp);
     $ausgabeTor = mysql_fetch_array ($result3);

     if ($ausgabeTor[0] > 0 || $ausgabeTor[1] > 0 ) {
     		 // Anz. Tore ggf. �bersetzen (z.B. bei Initialwert 99)
         $tor1 = correctTor($ausgabeTor[0], MIN_TORE, MAX_TORE);
         $tor2 = correctTor($ausgabeTor[1], MIN_TORE, MAX_TORE);
     }
    
		 //Faktor auslesen
     $aktuellFaktor = $ausgabeTor[2];
    
    //Wenn Faktor per ABSENDEN schon im POST, dann diesen anzeigen statt den in der DB (kommt nur vor, wenn nicht gespeichert werden konnte, weil Anzahl vergebener FPs zu die MAX FPs ueberstiegen haette!
     	// Aber genau in dem Fall soll ja dem user das angezeigt werden, was er gerne gesetzt haette, damit er es aendern kann, statt alles neu eintragen zu muessen!
    if (isset($_POST["F$ausgabe[SPIEL_ID]"])) {
	    $aktuellFaktor = $_POST["F$ausgabe[SPIEL_ID]"];
	  }
    
     	
     //summe gesetzter Faktorpunkte der gewaehlten kategorie hochzaehlen
     //falsch: $SGFK += $aktuellFaktor;
     
     //Bisher gesetzte Faktorpunkte des users aktualisieren
     $calc_sumfak=("SELECT SUM(Faktor) as totalF FROM tipp WHERE USER_ID=$userID");
     $calc_faksum=mysql_db_query($dbName,$calc_sumfak, $connect); 
     $faksum = mysql_fetch_array($calc_faksum); 
     $SummeFaktor =  $faksum[totalF];

          
     print ("$ausgabe[Datum]&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;Anpfiff: $ausgabe[Anpfiff]<br><br>");
     
     /******************************
            TABELLEN SETTINGS
     *******************************/
     print ('<table border="0">
            <colgroup>
            <col width="150">
            <col width="50">
            <col width="150">
            <col width="160">
                 </colgroup>');
     
     print("<td align=\"right\">
            $ausgabe[Name]
            <SELECT NAME='S$ausgabe[SPIEL_ID]_Team1'>");
     
     for ($index=0; $index <= MAX_TORE; $index++) {
         if ($tor1 == $index) {
             print ("<OPTION selected VALUE='$index'>$index");
         } else {
             print ("<OPTION VALUE='$index'>$index");
         }
     }
     print ("</SELECT> </td>");
           
     print("<td align=\"center\"> : </td>");
     
     print("<td>
     <SELECT NAME='S$ausgabe[SPIEL_ID]_Team2'>
         ");
     for ($index=0; $index <= MAX_TORE; $index++) {
         if ($tor2 == $index) {
             print ("<OPTION selected VALUE='$index'>$index");
         } else {
             print ("<OPTION VALUE='$index'>$index");
         }
     }

     print ("</SELECT>
            $ausgabe[Name2]</td>");
            
     print ("<td>Faktor <SELECT NAME='F$ausgabe[SPIEL_ID]'>");
     for ($index=1;$index<6;$index++) {
         if ($index == $aktuellFaktor) {
             print ("<OPTION selected VALUE='$index'>$index");
         } else {
             print ("<OPTION VALUE='$index'>$index");
         }
     }
     print ("</SELECT>");
     
     /****************************
     FLAGS unter dem Land anzeigen
     *****************************/
     print("<tr><td align=\"center\"> <p><img src=\"flags/$ausgabe[TEAM_ID1].gif\" alt=\"$ausgabe[Name]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\"></p>  </td>");
     print("<td></td>");
     print("<td align=\"center\">   <p><img src=\"flags/$ausgabe[TEAM_ID2].gif\" alt=\"$ausgabe[Name2]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\"></p>   </td></tr>");
     
     
     print ('</td></table>');
     print ("<br><hr>");

   } else {

       /*****************************************************************************
                 es darf nicht mehr getippt werden - (Anpfiff schon vorbei!)
       ******************************************************************************/
       
       
       $getTipp = "SELECT t.Tore1, t.Tore2, t.Faktor, t.TippPunkte FROM tipp t WHERE USER_ID=$userID AND SPIEL_ID=$ausgabe[SPIEL_ID]";
       $result3 = mysql_query($getTipp);
       $ausgabeTor = mysql_fetch_array ($result3);
       
       //Wenn ein Tipp abgegeben wurde
			 // Achtung: Anzahl Tore wird als "---" angezeigt, wenn initial (�bersetzung des Initialwertes 99).
			 //          Dies liegt daran, dass der Benutzer sofort sehen muss, dass sein nicht abgegebener Tipp
			 //          keine Punkte bringen kann. In die DB kann man "-1" leider nicht initial eintragen, ohne
			 //          den unsigned-Typ von "Tore1" und "Tore2" zu �ndern.
       if ($ausgabeTor[0] > -1 || $ausgabeTor[1] > -1 ) {
           $tor1 = correctTor($ausgabeTor[0], MIN_TORE, MAX_TORE, "---");
           $tor2 = correctTor($ausgabeTor[1], MIN_TORE, MAX_TORE, "---");
       }
       //Faktor auslesen und hochzaehlen
       $aktuellFaktor = $ausgabeTor[2];

	//summe gesetzter Faktorpunkte der gewaehlten kategorie hochzaehlen
	//falsch: $SGFK += $aktuellFaktor;

      
       $SpielPunkte = $ausgabeTor[TippPunkte] * $aktuellFaktor;
	
	print ("<a href=\"view_gametipps.php?gid=$ausgabe[SPIEL_ID]\"> Wie haben die anderen getippt? </a><br>");
       print(" Dein Tipp:<br>");
       /******************************
       TABELLEN SETTINGS
       *******************************/
       print ('<table border="0">
              <colgroup>
              <col width="150">
              <col width="20">
              <col width="150">
              <col width="160">
              </colgroup>');
       
       /****************************
       FLAGS ueber dem Land anzeigen
       *****************************/
       print("<tr><td align=\"right\"> <p><img src=\"flags/$ausgabe[TEAM_ID1].gif\" alt=\"$ausgabe[Name]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\">&nbsp&nbsp&nbsp&nbsp</p></td>");
       print("<td></td>");
       print("<td align=\"left\">   <p>&nbsp&nbsp&nbsp&nbsp<img src=\"flags/$ausgabe[TEAM_ID2].gif\" alt=\"$ausgabe[Name2]\" width=\"". FLAG_WIDTH. "\" height=\"". FLAG_HEIGHT. "\"></p>   </td></tr>");
       
       /************
       Team - Names
       ************/
       print ("<tr><td align=\"right\">$ausgabe[Name] </td><td align=\"center\">:</td><td>$ausgabe[Name2]</td><td>$tor1 : $tor2</td></tr>");


              
       
                                                                               
       /******
       NUR WENN SPIEL-ERGEBNIS vom admin eingetragen:
       *******/
       if (($ausgabe[Tore1] > -1)&&($ausgabe[Tore2] > -1)) {
           
           //DB-Anfrage fuer Besten Tipp dieses Spiels zu holen
           $db_get_bestTipp="SELECT USER_ID, TippPunkte FROM tipp WHERE SPIEL_ID=$ausgabe[SPIEL_ID] ORDER BY TippPunkte DESC";
           $get_bestTipp_result = mysql_query($db_get_bestTipp);
           $ausgabe_get_bestTipp = mysql_fetch_array ($get_bestTipp_result);       
           
           //DB-Anfrage fuer MEISTE PUNKTE dieses Spiels zu holen
           $db_get_mostPoints="SELECT USER_ID, SpielPunkte FROM tipp WHERE SPIEL_ID=$ausgabe[SPIEL_ID] ORDER BY SpielPunkte DESC";
           $get_mostPoints_result  = mysql_query($db_get_mostPoints);
           $ausgabe_get_mostPoints = mysql_fetch_array ($get_mostPoints_result);       
           
           
           print ("<tr><td></td><td></td><td align=\"right\">Endergebnis: &nbsp&nbsp</td><td>$ausgabe[Tore1] : $ausgabe[Tore2] </td></tr>");
           print ("<tr><td></td><td></td><td align=\"right\">Tipp-Punkte: &nbsp&nbsp</td><td>$ausgabeTor[TippPunkte]</td>");
           if ($ausgabe_get_bestTipp[USER_ID] == $userID)
                                {
                                    print("<td>  <p><img src=\"pics/best_Tipp.gif\" alt=\"-->BESTER TIPP<--\"></p>  </td>");
                                }
           
           print("</tr>");
           print ("<tr><td></td><td></td><td align=\"right\">Faktor: &nbsp&nbsp</td><td>$aktuellFaktor</td></tr>");
           print ("<tr><td></td><td></td><td align=\"right\">Spiel-Punkte: &nbsp&nbsp</td><td>$SpielPunkte </td>");
           
           
           if ($ausgabe_get_mostPoints[USER_ID] == $userID)
           {
               print("<td>  <p><img src=\"pics/most_Points.gif\" alt=\"-->MEISTE PUNKTE<--\"></p>  </td>");  
           }
           print("</tr>");

       }       
       print("</table>");
       print(" <br><hr>");

   }


 }
 /* falsch:
		 //Die Summe der gesetzten Faktorpunkte in dieser Kategorie VOR Speicherung uebermitteln:
		 if (isset($_POST[SGFK]))
			$SGFK -= $_POST[SGFK];
		 echo'<input type="hidden" name="SGFK" value="'.$SGFK.'">';
*/

if (debug()) {
  print "<pre>faktorMax = $faktorMax</pre>";
  print "<pre>faktorMin = $faktorMin</pre>";
  print "<pre>SummeFaktor = $SummeFaktor</pre>";
}

if (!$SummeFaktor) {  // d.h. diese Seite wurde zum 1. Mal aufgerufen
  $SummeFaktor = $faktorMax - $faktorMin;
  if (debug()) { print "<pre>SummeFaktor neu = $SummeFaktor</pre>"; }
}

// Anz. restl. Faktorpunkte insges.
$RestFaktorPunkte = $faktorMax - $SummeFaktor;
if ($RestFaktorPunkte > $faktorMax)
{
    $RestFaktorPunkte = $faktorMax;
}

// Durchschnittl. Anzahl Faktorpunkte pro Spiel
//  - Minium: 1
//  - bei n Spielen: zusaetzl. n Faktorpunkte
//  - avg = 1 + restl. Faktorpunkte / Anz. zukuenftiger Spiele
$avgFPS = 1 + $RestFaktorPunkte / $anzRestSpiele;
if (debug()) { print "<pre>"; print (rfpAsString($RestFaktorPunkte, $avgFPS)); print "</pre>"; }

print("<table width=\"50%\" bgcolor=\"#000000\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" align=\"center\">");

if ($zuvielefaktorpunktevergeben == true)
{
	print ("<th bgcolor=\"#FFe7e7\" align=\"center\" colspan=\"0\">");
  $text = "Bitte vergib ". singplur("einen", $differenz, $differenz) . " ". singplur("Faktorpunkt", "Faktorpunkte", $differenz). " weniger!";
  print ($text);
}
elseif(isset($_POST[submit]))
{
	print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">");
  print (" Deine Daten wurden gespeichert!<br>");
  print(rfpAsString($RestFaktorPunkte, $avgFPS));
}
else
{
		if (debug()) {
  		// Durchschnittl. Anzahl Faktorpunkte pro Spiel
  		//  - Minium: 1
  		//  - bei n Spielen: zusaetzl. n Faktorpunkte
  		//  - avg = 1 + restl. Faktorpunkte / Anz. zukuenftiger Spiele
  		//$avgFPS = $RestFaktorPunkte / $faktorMin; // $faktorMin = min. setzbare Faktoren, also Anz. Spiele!
  		//$avgFPS = $faktorMax / $SummeFaktor; // $faktorMin = min. setzbare Faktoren, also Anz. Spiele!
			print("<br><br><hr><br>
				Restl. Faktorpunkte: $RestFaktorPunkte <br>
				Restl. Spiele: $anzRestSpiele <br>
				Durchschnittl. Faktorpunkte pro Spiel: &#216;");
				printf("%.2f",$avgFPS); 
		}

	print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">");
  print(rfpAsString($RestFaktorPunkte, $avgFPS));
}

print("<br></th>");
print("</table>");
print("<br><hr><br>");
?>
<input type="submit" name="submit" value=" Absenden ">
</form>
</body>
</html>
