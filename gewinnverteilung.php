<?php 
/* Ranking */
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);

// Zur Vereinfachung
$user = $_SESSION['user'];

// Einsatz aus settings auslesen
$db_get_einsatz = "SELECT Einsatz FROM settings";
$get_einsatz_result = mysql_query($db_get_einsatz);
$ausgabe_get_einsatz = mysql_fetch_row ($get_einsatz_result);
$einsatz = $ausgabe_get_einsatz[0];

// schnelles pow (x^y)
function powInt($x, $y) {
  $res = 1;
  for ($i=0; $i < $y; $i++) {
    $res *= $x;
  }
  return $res;
}

/**************************************************************************************
  Verteilungsfunktionen
  Um den Gewinn zu verteilen, kann man sich einer der folgenden Funktionen bedienen.
	Zum Testen das DEBUG-Flag in general_defs.inc.php auf true setzen (am besten nur fuer 
	bestimmte Benutzer) und test_verteilung aufrufen.
	Achtung: neue Verteilungsfunktionen müssen dort eingepflegt werden!
 **************************************************************************************/

// Verteilungsfunktion: f(x) = 1/2(x^2)
function verteilung1($n) {
  return (powInt($n, 2)) / 2;
}

// Verteilungsfunktion: f(x) = (x^7/4)
function verteilung2($n) {
  return (powInt($n, 7/4));
}

// Verteilungsfunktion: f(x) = 1/3(x^3)
function verteilung3($n) {
  return (powInt($n, 3)) / 3;
}

// Verteilungsfunktion: f(x) = (x^3/2)
function verteilung4($n) {
  return (powInt($n, 1.5));
}

// Gibt den Faktor zurück, mit dem die Werte aus der Verteilungsfunktion
// multipliziert werden müssen, um die Verteilung auf den vorgegebenen 
// Gesamtgewinn zu normieren.
// Bsp.:       
// Anz. Spieler n    = 12 
// Anz. Gewinner g   = 4
// Verteilung:  f(x) = 1/2(x^2)
// Verteilungssume (=Summe der Verteilung von Gewinner 0-3)
//   = f(0) + f(1) + f(2) + f(3) 
//   =    0 +  1/2 +    2 +  9/2
//   = 7
// Ergo ist der Faktor zur Normierung auf 12 Spieler 12/7!
function calc_verteilung_faktor($numOfPlayers, $numOfWinners, $verteilung) {
  $sum = 0;
  for($i=0; $i < $numOfWinners; $i++) {
    $sum += $verteilung($i);
  }

  $factor = $numOfPlayers/$sum;

	if (debug()) {
		print "<br>";
		print "sum          = $sum<br>";
		print "numOfPlayers = $numOfPlayers<br>";
		print "factor       = $factor<br>";
	}
  return $factor;
}

// Besorgt sich die doppelten Einträge eines arrays, d.h.
// gibt ein array zurück, dessen key = die Position des LETZTEN
// gefundenen Eintrags ist und dessen value = der value im Array ist.
function array_duplicates($array){ 
	return array_unique(array_diff_assoc($array,array_unique($array))); 
}

// Füllt den Gewinn des 1. derart auf, dass er mindestens einen Euro Abstand zum 2. mehr hat
// als der 2. zum 3.. Dabei wird von unten nach oben aufgefüllt.
function fill_from_start($gewinne, $min_diff) {
	
	$count = count($gewinne);
  // Array zu klein: hier passiert der Fehler ohnehin nicht
	if ($count <= 3) return $gewinne;

  $res = $gewinne;
	$abstand = 3; // vergrößert sich nach jeder Erhöhung
  for ($i = 1; $i < $count; $i++) {

		$diff = $res[$i] - $res[$i-1];
		if ($diff < $abstand) continue;
		 
		$res[count($res)-1]++;
		$res[$i]--;
		$abstand++;
		$min_diff--;
	}

  if ($min_diff > 0) { // hat nicht geklappt: es konnten nicht alle fehlenden Punkte ergänzt werden
	  $res = $gewinne; // Änderungen rückgängig machen
	}
	
	$sum=0;
	foreach(array_keys($res) as $key) $sum += $res[$key];
	if (debug()) { print "Neue Summe des Vektors:". $sum ."<br>"; }
	
	return $res;
}

// Bei der Berechnung der Gewinnsumme für alle Gewinner kann es passieren,
// dass manche Gewinne gleich groß sind. Diese werden hier entsprechend 
// geändert.
function finish_gewinne($gewinne) {
	
	$res = $gewinne;
	//if (debug()) { print "<pre>". var_dump($res). "</pre>"; }
  $help = array_duplicates($res); 
	//if (debug()) { print "<pre>". var_dump($help). "</pre>"; }
	
	while (count($help)> 0) {
	  foreach(array_keys($help) as $key){
		
		  /*if (debug()) {
				print "count(help) = ". count($help) . "<br>";
				print "key         = $key<br>";  
				print "res[$key]   = ". $res[$key]. "<br>";  				

			}*/
		
			$res[$key]++;
			$res[count($res)-1]--;
		}
		
    //if (debug()) { print "<pre>". var_dump($res). "</pre>"; }
		$help = array_duplicates($res);
  	//if (debug()) { print "<pre>". var_dump($help). "</pre>"; }
	}

  // Prüfung: wenn der Abstand des 1. zum 2. kleiner oder gleich des Abstands
	//          vom 2. zum 3. ist, muss wieder von unten aufgefüllt werden!
	$cnt = count($res);
	if ($cnt > 3) {
	  $diffFirstSecond = $res[$cnt-1]-$res[$cnt-2];
		$diffSecondThird = $res[$cnt-2]-$res[$cnt-3];
    $diff = $diffSecondThird - $diffFirstSecond;
		if ($diff >= 0) {
      $min_diff = $diff+1; // so viele Euro muss der 1. noch dazu bekommen
			$res = fill_from_start($res, $min_diff);
		}
	}

  if (debug()) {
		print "<br>Gewinne nach finish_gewinne:<br>";
    foreach(array_keys($res) as $key) {
 			 print "<b>gewinn($key) = ". $res[$key]. "</b><br>";
		}
		print "<br>";
	}

  return $res;
}

// Berechnet die Gewinnsumme für alle Gewinner aus den geg. Daten
// (ohne Berücksichtigung der Punkte der Kandidaten)
function calc_gewinne($numOfPlayers, $numOfWinners, $gesamtgewinn, $einsatz, $verteilung) {
	
	if (debug()) {
		print "numOfPlayers = $numOfPlayers<br>";
		print "numOfWinners = $numOfWinners<br>";
		print "gesamtgewinn = $gesamtgewinn<br>";
		print "einsatz      = $einsatz<br>";
		print "verteilung   = $verteilung<br>";  
	}
	
	$res = array();
	
	// Wenn nur 1 Spieler gewinnt, ist die Sache einfach...
	if ($numOfWinners == 1) {
		$res[0] = $gesamtgewinn;
		return finish_gewinne($res); // hauptsächl. wegen der Ausgaben, an sich unnötig
	}
		
  // Alle Gewinner bekommen mind. ihren Einsatz zurück, d.h.
  // der zu verteilende Gewinn schrumpft um (Gewinner*$einsatz)
  $gewinn_zu_verteilen = ($numOfPlayers-$numOfWinners)*$einsatz;
  if (debug()) print "gewinn_zu_verteilen = $gewinn_zu_verteilen<br>";
  
  $verteilungs_faktor = calc_verteilung_faktor($numOfPlayers, $numOfWinners, $verteilung);
  if (debug()) print "verteilungs_faktor = $verteilungs_faktor<br>";
  
  // FIXME!!! Benutze $rang hier später!
  $sumAbs = 0;
  $sumFull = 0;  
  for ($i=0; $i < $numOfWinners; $i++) {
    $gewinn_rel = $verteilung($i) * $verteilungs_faktor;
    if (debug()) print "gewinn_rel($i)  = $gewinn_rel<br>";
	
		$gewinn_abs = floor($gewinn_rel * $gewinn_zu_verteilen / $numOfPlayers);
		if (debug()) print "gewinn_abs($i)  = $gewinn_abs<br>";

    $gewinn_full = $gewinn_abs + 10; // = die 10 Euro, die wir vorher rausgerechnet haben
    if (debug()) print "<b>gewinn_full($i) = $gewinn_full</b><br>";
	
		$sumAbs += $gewinn_abs;
		$sumFull += $gewinn_full;
		
		if ($i == $numOfWinners-1) { // d.h. der Gewinn des Ersten wird berechnet
	
				// Verteile den Rest, der (z.B. durch Rundung) entstanden ist, auf den Gewinner
			$gewinn_abs  += ($gewinn_zu_verteilen - $sumAbs);
			$gewinn_full += ($gesamtgewinn - $sumFull);

			if (debug()) {
				print "gewinn_abs($i) neu  = $gewinn_abs<br>";
				print "<b>gewinn_full($i) neu = $gewinn_full</b><br>";
	
				print "sumAbs  original = $sumAbs<br>";
				print "sumFull original = $sumFull<br>";
			}
			$sumAbs = $gewinn_zu_verteilen;
			$sumFull = $gesamtgewinn;
		}
		
		$res[$i] = $gewinn_full; // Speichere die Gewinne im Array
  }

	if (debug()) {
		print "sumAbs  = $sumAbs<br>";
		print "sumFull = $sumFull<br>";
	}  
	
	return finish_gewinne($res);
}

// Sortiert das Ranking nach Punkten
function sortiere_nach_punkten(&$ranking) {

	$last_punkte = 0;
	$gewinn_rang = 1;
	$last_gewinn_rang = $gewinn_rang;
	foreach (array_keys($ranking) as $key)
	{
		$punkte  = $ranking[$key]["punkte"];		
		if ($punkte == $last_punkte) {
			$ranking[$key]["rang"] = $last_gewinn_rang;
		} else {
   		$gewinn_rang = $key+1;
  		$ranking[$key]["rang"] = $gewinn_rang;
		}
		
		$last_punkte      = $punkte;
		$last_gewinn_rang = $gewinn_rang;
  }

	if (debug()) {
		print "<br>";
	  foreach(array_keys($ranking) as $key) {
			print "spieler   = ". $ranking[$key]["user"]. "<br>";
			print "rang      = ". $ranking[$key]["rang"]. "<br>";
			print "punkte    = ". $ranking[$key]["punkte"]. "<br>";			
		}
		print "<br>";
	}
}

// Bereinigt die Gewinnsummen unter Berücksichtigung der Platzierung der Kandidaten.
// Mehrfach besetzte Ränge teilen sich die Summe der Gewinne aus diesen Rängen.
// Bsp.:
// Platz 1 = 80 Euro
// Platz 2 = 50 Euro
// Platz 3 = 30 Euro
// Platz 1 doppelt besetzt bedeutet:
// Platz 1 (doppelt) =  2 x 65 Euro (jeweils die Hälfte der Summe aus Platz 1 + 2)
// Platz 2 -> existiert nicht mehr (mit Platz 1 fusioniert)
// Platz 3           = 30 Euro (keine Änderung)
function bereinige_gewinne(&$ranking, $gewinne) {
	
  sortiere_nach_punkten($ranking);

  $cnt = 0;
	$last_rang = 1;
	$gewinne_neu_keys   = array();
	$gewinne_neu_values = array();	
	$start = 0;
	foreach (array_keys($ranking) as $key) {
	  $rang = $ranking[$key]["rang"];

		$gewinne_neu_keys[$key]   = $key;
		$gewinne_neu_values[$key] = -1;

		if (debug()) {
		  print "rang      = $rang<br>";
			print "last_rang = $last_rang<br>";
		}

    if ($rang == $last_rang) {
      $cnt++;
  		if (debug()) {	print "cnt       = $cnt<br>"; }
			continue;
		}

		$offset = $rang - $start - 1;
		if (debug()) {
			print "<b>start  = $start</b><br>";
			print "<b>offset = $offset</b><br>";
		}

  	$toShare = array_sum(array_slice($gewinne, $start, $offset)); // Zu (evtl.) teilende Summe
		if (debug()) {
		  print "last_rang = $last_rang<br>";
			print "toShare = $toShare<br>";
		}
		$toShare /= ($cnt-$start);

		if (debug()) {
		  print "cnt = $cnt<br>";
			print "start = $start<br>";
			print "<b>toShare = $toShare</b><br>";
		}

	  for($i = $start; $i < $cnt; $i++) {
  		$gewinne_neu_values[$i] = $toShare;
		}
		$start = $cnt;
		$cnt++;
		$last_rang = $rang;
	}
	
	// Zum letzten Mal noch den Gewinn eintragen
	// (besonders wichtig, wenn noch niemand Punkte hat, d.h. ALLE Spieler auf Rang 1 sind!)
	$offset = $last_rang - $start;
	if (debug()) {
	  print "<b>start  = $start</b><br>";
		print "<b>offset = $offset</b><br>";
	}

  if ($start == 0 && $last_rang == 1) { // d.h. Initialwerte, d.h. ALLE Spieler haben die gleiche Punktzahl)
    $offset = count($gewinne); // damit werden ALLE Gewinnsummen aufaddiert
		if (debug()) { print "<b>offset (changed) = $offset</b><br>"; }
	}

	$toShare = array_sum(array_slice($gewinne, $start, $offset)); // Zu (evtl.) teilende Summe
	if (debug()) {
		print "cnt     (end) = $cnt<br>";
		print "start   (end) = $start<br>";
		print "toShare (end) = $toShare<br>";
	}
	$toShare /= ($cnt-$start);
	if (debug()) {
		print "<b>toShare (end2) = $toShare</b><br>";
	}

	for($i = $start; $i < $cnt; $i++) {
		$gewinne_neu_values[$i] = $toShare;
	}
	
	$gewinne_neu = array_combine($gewinne_neu_keys, $gewinne_neu_values);

	if (debug()) {
   	print "<pre>gewinne_neu_keys   = ". var_dump($gewinne_neu_keys).   "</pre><br>";
   	print "<pre>gewinne_neu_values = ". var_dump($gewinne_neu_values). "</pre><br>";
   	print "<pre>gewinne_neu        = ". var_dump($gewinne_neu).        "</pre><br>";				
	}
  return $gewinne_neu;
}


function test_verteilungen() {

	print ('<table align="center" border="1">
				 <colgroup>
				 <col width="250">
				 <col width="650">
				 </colgroup>');
	print ("<th align=\"center\">Funktion</th><th>Verteilung</th>");
	print ("<tr valign=\"middle\" align=\"center\">");
	print ("<td>");
	print "<b>Verteilung 1: f(x) = 1/2(x^2)</b><br>";
	print "----------------------------------------<br>";
	print ("</td>");
	print ("<td>");
	$test = calc_gewinne($numOfPlayers, $numOfWinners, $gewinn_max, $gewinn_min, verteilung1);
	print ("</td>");
	print ("</tr>");
	
	print ("<tr valign=\"middle\" align=\"center\">");
	print ("<td>");
	print "<b>Verteilung 4: f(x) = x^(7/4)</b><br>";
	print "----------------------------------------<br>";		
	print ("</td>");		
	print ("<td>");
	$test = calc_gewinne($numOfPlayers, $numOfWinners, $gewinn_max, $gewinn_min, verteilung2);
	print ("</td>");
	print ("</tr>");
	
	print ("<tr valign=\"middle\" align=\"center\">");
	print ("<td>");
	print "<b>Verteilung 3: f(x) = 1/3(x^3)</b><br>";
	print "----------------------------------------<br>";		
	print ("</td>");
	print ("<td>");
	$test = calc_gewinne($numOfPlayers, $numOfWinners, $gewinn_max, $gewinn_min, verteilung3);
	print ("</td>");
	print ("</tr>");

	print ("<tr valign=\"middle\" align=\"center\">");
	print ("<td>");
	print "<b>Verteilung 4: f(x) = x^(3/2)</b><br>";
	print "----------------------------------------<br>";		
	print ("</td>");
	print ("<td>");
	$test = calc_gewinne($numOfPlayers, $numOfWinners, $gewinn_max, $gewinn_min, verteilung4);
	print ("</td>");
	print ("</tr>");

	print ("</table>");		
}

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Aktuelle Gewinnverteilung"); 
print $head;
?>


<body>

<?php
$menu = create_menu();
print $menu;
?>

<div align="center">
In der Tabelle unten siehst Du, wie die Gewinnverteilung aktuell aussieht.
<br><br>
Beachte, dass die <b>Verteilung von der Anzahl der Teilnehmer abh&auml;ngt.</b> Verteilt wird der gesamte Einsatz<br>
auf das obere Drittel der Tippgemeinschaft (abgerundet), und zwar mit einer exponentiellen Verteilungsfunktion.
<br><br>
<b>Wichtig:</b> stehen mehrere Kandidaten auf dem gleichen Platz, teilen sie sich den Gewinn aus der Summe der Pl&auml;tze.<br><br>

<b>Beispiel:</b><br>
Der Gewinn für Platz 1 sei <b>80 Euro</b>, für Platz 2 <b>50 Euro</b>, zusammen <b>130</b> Euro. Stehen 2 Leute auf Platz 1, bekommt jeder <b>65</b> Euro.<br>
Dabei kann es durchaus vorkommen, dass der Spieler auf dem nachfolgenden Platz mehr gewinnt als die Spieler vor ihm, wenn er nichts teilen muss!
<br><br>		
</div>

<?php

  // Test: Verteilungen
	//test_verteilungen();

	/********************************
	 Berechnung der Gewinnverteilung
	*********************************/
  $query = "SELECT user, TotalPoints 
						FROM user 
						WHERE user != 'admin' AND user != 'Gast'";
						
	if ($_SESSION["user"] != "admin") { 
	  $query .= " AND user NOT LIKE 'test%'";
	}
  $query .=	" ORDER BY TotalPoints DESC, user ASC";
	$db_getWinners = mysql_query($query);

	$numOfPlayers = mysql_num_rows($db_getWinners);
	$gesamtgewinn = $numOfPlayers * $einsatz; // = alle Teilnehmer * Einsatz
	$numOfWinners = floor($numOfPlayers/3); // nur das obere Drittel (abgerundet) gewinnt!

	// Rang eines jeden Spielers erfassen
	$ranking = array();
	$rang = 0;
	while ($ausgabe_get_Winners = mysql_fetch_array ($db_getWinners)) {
		$ranking[$rang] = array("user"   => $ausgabe_get_Winners["user"],
		                        "punkte" => $ausgabe_get_Winners["TotalPoints"]);
		// FIXME!!! TEST!!! Funzt nur in dieser Reihenfolge (vom 1. in der Liste abwärts)
		//if ($ausgabe_get_Winners["user"] == "angelika") $ranking[$rang]["punkte"] = 20;
		//if ($ausgabe_get_Winners["user"] == "dominik") $ranking[$rang]["punkte"] = 20;
		//if ($ausgabe_get_Winners["user"] == "Kampfschnitzel") $ranking[$rang]["punkte"] = 10;
		//if ($ausgabe_get_Winners["user"] == "Manolo") $ranking[$rang]["punkte"] = 10;
		//if ($ausgabe_get_Winners["user"] == "Rika1960") $ranking[$rang]["punkte"] = 10;
		$rang++;
	}

	if (debug()) {
  	  print "<pre>". var_dump($ranking). "</pre><br>";
	}
	
  // Gewinne berechnen (ohne Berücksichtigung gleicher Punkte)	
	$gewinn_min = $einsatz;
	$gewinn_max = $gesamtgewinn;
  $gewinne = calc_gewinne($numOfPlayers, $numOfWinners, $gewinn_max, $gewinn_min, verteilung4);
  $gewinne = array_reverse($gewinne); // Sortierung korrigieren (vorher: vom LETZTEN zum ERSTEN Gewinner)

	if (debug()) {
		print "numOfPlayers = $numOfPlayers<br>";
		print "numOfWinners = $numOfWinners<br>";
		print "gesamtgewinn = $gesamtgewinn<br>";
		print "einsatz      = $einsatz<br>";
		foreach(array_keys($gewinne) as $key) {
       print "gewinn($key) = ". $gewinne[$key]. "<br>";
		} 
	}	

  // Gewinne bereinigen (gleiche Punkte berücksichtigen)
  $gewinne_neu = bereinige_gewinne($ranking, $gewinne);

	/******************************
 	 AUSGABE
	*******************************/
	print ('<table align="center" border="10">
				 <colgroup>
				 <col width="30">
				 <col width="150">
				 <col width="50">
				 <col width="50">				 
				 </colgroup>');
	print("<th align=\"center\">Rang</th><th>Spieler</th><th>Punkte</th><th>Gewinn in €</th>");

	foreach (array_keys($ranking) as $key)
	{
		$spieler      = $ranking[$key]["user"];
		$punkte       = $ranking[$key]["punkte"];		
		$gewinn_rang  = $ranking[$key]["rang"];		
		$gewinn       = $gewinne_neu[$key];
		
		if ($gewinn != intval($gewinn)) { 
			$gewinn = sprintf("%.2f", $gewinn); 
		}
		
		print ("<tr valign=\"bottom\" align=\"center\">");
		print ("<td>$gewinn_rang</td>");
		print ("<td>$spieler</td>");
		print ("<td>$punkte</td>");
		print ("<td>$gewinn</td>");
		print ("</tr>");
	}
	print("</table>");
?>
                
</body>
</html>
