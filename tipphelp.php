<?php 
/***************************************************************************
*** Hier kann sich jeder Eingeloggte ueber die Regeln informieren 	********	
*** wie kann ich Punkte sammeln, wie geht das mit den Faktorpunkten usw. ***
****************************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
//$head = create_head("Mein Turniersieger-Tipp"); 
//print $head;
// Achtung: styles-Definition!!! (--> ab nach styles.css???)
?>


<head>
<title>beTTer - so gehts</title>
<link rel="stylesheet" type="text/css" href="style.css">

<style>
<!--
 p.MsoNormal
	{
	margin-bottom:.0001pt;
	font-size:12.0pt;
	font-family:"Times New Roman";
	margin-left:0cm; margin-right:0cm; margin-top:0cm}
 li.MsoNormal
	{
	margin-bottom:.0001pt;
	font-size:12.0pt;
	font-family:"Times New Roman";
	margin-left:0cm; margin-right:0cm; margin-top:0cm}
table.MsoTableList2
	{border-left:medium none; border-right:medium none; border-top:medium none; border-bottom:1.5pt solid gray; font-size:10.0pt;
	font-family:"Times New Roman"}
-->
</style>
</head>

<body>

<?php  

$menu = create_menu();
print $menu;

/******************************************************
***** Daten bzgl. der Turnier-Einstellungen holen *****
*******************************************************/
	$dbanfrage = "	SELECT *
					FROM settings ";                
	$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
	$ausgabe = mysql_fetch_array ($result);

?>

<p class="MsoNormal" align="center"><u><b><font size="5">Tippen</font></b></u></p>
<p class="MsoNormal" align="center">&nbsp;</p>
<p class="MsoNormal">Zu jedem Spiel wird das <b>Endergebnis</b> getippt, d.h. NICHT das Ergebnis nach 90 Minuten und auch NICHT das Ergebnis nach
Elfmeterschie&szlig;en,<br>
sondern das <b>Gesamtergebnis nach Spielende</b>. Beispiele:<br>
<ul style="margin-top: 0cm; margin-bottom: 0cm" type="disc">
<br>
  <li class="MsoNormal">Ergebnis nach 90 Minuten: <b>3:2</b> => Ergebnis: <b>3:2</b>.</li>
  <li class="MsoNormal">Ergebnis nach 120 Minuten: <b>3:2</b> => Ergebnis: <b>3:2</b>.</li>
  <li class="MsoNormal">Ergebnis nach 120 Minuten: <b>2:2</b>, dann Elfmeterschie&szlig;en mit <b>5:4</b> => Ergebnis: <b>7:6</b>.</li>  
</ul>
<br>

Punkte kannst du nach dem folgendem Schema sammeln:<br></p>
<ul style="margin-top: 0cm; margin-bottom: 0cm" type="disc">
  <li class="MsoNormal">Für ein <b>korrekt getipptes Ergebnis</b> kassierst du <b>
	  <?php 
	  	print("$ausgabe[Punkte_korrekter_Tipp] Punkt"); 
		if ( $ausgabe[Punkte_korrekter_Tipp] != 1 )
		{
			print("e");
		}
		?>
		</b>		
.</li>
  
  <li class="MsoNormal">Stimmt die <b>Tordifferenz</b> deines Tipps, bekommst du noch <b>
   <?php 
   	print("$ausgabe[Punkte_korrekte_Tore] Punkt");
		if ( $ausgabe[Punkte_korrekte_Tore] != 1 )
		{
			print("e");
		}
		?>
		</b>
.</li>
  
  <li class="MsoNormal">Hast Du den <b>richtigen Sieger</b> gesetzt, gibt es immer noch <b>
  <?php 
  	print("$ausgabe[Punkte_korrekter_Sieger] Punkt");
	if ( $ausgabe[Punkte_korrekter_Sieger]  != 1 )
		{
			print("e");
		}
     	?>
		</b>		
   auf dein Konto.</li>
</ul>
<p class="MsoNormal"><br>Beispiel:</p>
<table class="MsoTableList2" border="1" cellspacing="0" cellpadding="0" style=" border: medium none; margin-left: 35.55pt">
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">Dein Tipp:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">Deutschland : Holland&nbsp; 3:1</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">Spielausgang:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">3:1 --- 
		  <?php 
	  	print("<b>$ausgabe[Punkte_korrekter_Tipp]</b> Punkt"); 
		if ( $ausgabe[Punkte_korrekter_Tipp] != 1 )
		{
			print("e");
		}
		?>
	
	 (exaktes Ergebnis getippt)</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">Spielausgang:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">2:0 --- 
	   <?php 
   	print("<b>$ausgabe[Punkte_korrekte_Tore]</b> Punkt");
		if ( $ausgabe[Punkte_korrekte_Tore] != 1 )
		{
			print("e");
		}
		?>
	
	 (Tordifferenz korrekt getippt)</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">Spielausgang:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">5:0 --- 	   <?php 
   	print("<b>$ausgabe[Punkte_korrekter_Sieger]</b> Punkt");
		if ( $ausgabe[Punkte_korrekter_Sieger] != 1 )
		{
			print("e");
		}
		?>&nbsp; (auf den richtigen Sieger getippt)</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">Spielausgang:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">0:1 --- <b>0</b> Punkte (Leider falsch getippt)</td>
  </tr>
</table>
<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal">&nbsp;</p>


<p class="MsoNormal" align="center"><u><b><font size="5">Faktor-Punkte</font></b></u></p>
<p class="MsoNormal" align="center">&nbsp;</p>
<p class="MsoNormal">Zusätzlich kann jeder Spieler seine Tipps verschieden stark 
gewichten, indem er Faktorpunkte auf seine Tipps verteilt.</p>
<p class="MsoNormal">Jeder Spieler hat 
<?php 
   	print("$ausgabe[Anzahl_Faktorpunkte]"); 
?>
	Faktorpunkte, die er auf die Spiele 
verteilen kann. Pro Spiel muss man mindestens einen und kann höchstens 5 
Faktorpunkte einsetzen. </p>
<p class="MsoNormal">Bei einem Faktorpunkt wird die erreichte Punktzahl einfach 
gewertet, bei zwei Faktorpunkten wird sie verdoppelt, bei drei verdreifacht usw.</p>
<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal">Beispiel:</p>
<table class="MsoTableList2" border="1" cellspacing="0" cellpadding="0" style="border: medium none; margin-left: 35.55pt">
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">Dein Tipp:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">Deutschland : Holland&nbsp; 3:1</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">Spielausgang:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: #EFFFEF">
    <p class="MsoNormal">3:1 --- 
	<?php 
   	print("<b>$ausgabe[Punkte_korrekter_Tipp]</b> Punkt");
		if ( $ausgabe[Punkte_korrekter_Tipp] != 1 )
		{
			print("e");
		}
		?>	
	(exaktes Ergebnis getippt)</td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">1 Faktorpunkt:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">
	1 * <?php print("$ausgabe[Punkte_korrekter_Tipp]");
	$Points= 1* $ausgabe[Punkte_korrekter_Tipp];
	print(" = $Points --- <b>$Points</b> Punkt");
	if ( $Points != 1 )
		{
			print("e");
		}

	print(" aufs Konto");
	
	?> </td>
  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: white">
    <p class="MsoNormal">2 Faktorpunkte:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: white">
    <p class="MsoNormal"> 
	2 * <?php print("$ausgabe[Punkte_korrekter_Tipp]");
	$Points= 2* $ausgabe[Punkte_korrekter_Tipp];
	print(" = $Points --- <b>$Points</b> Punkt");
	if ( $Points != 1 )
		{
			print("e");
		}

	print(" aufs Konto");
	
	?> </td>

  </tr>
  
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm ">
    <p class="MsoNormal">3 Faktorpunkte:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">
	3 * <?php print("$ausgabe[Punkte_korrekter_Tipp]");
	$Points= 3* $ausgabe[Punkte_korrekter_Tipp];
	print(" = $Points --- <b>$Points</b> Punkt");
	if ( $Points != 1 )
		{
			print("e");
		}

	print(" aufs Konto");
	
	?> </td>

  </tr>
	
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: white">
    <p class="MsoNormal">4 Faktorpunkte:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm; background: white">
    <p class="MsoNormal">4 * <?php print("$ausgabe[Punkte_korrekter_Tipp]");
	$Points= 4* $ausgabe[Punkte_korrekter_Tipp];
	print(" = $Points --- <b>$Points</b> Punkt");
	if ( $Points != 1 )
		{
			print("e");
		}

	print(" aufs Konto");
	
	?> </td>

  </tr>
  <tr>
    <td width="163" valign="top" style="width: 122.4pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">5 Faktorpunkte:</td>
    <td width="348" valign="top" style="width: 261.0pt; border: medium none; padding-left: 5.4pt; padding-right: 5.4pt; padding-top: 0cm; padding-bottom: 0cm">
    <p class="MsoNormal">
	5 * <?php print("$ausgabe[Punkte_korrekter_Tipp]");
	$Points= 5* $ausgabe[Punkte_korrekter_Tipp];
	print(" = $Points --- <b>$Points</b> Punkt");
	if ( $Points != 1 )
		{
			print("e");
		}

	print(" aufs Konto");
	?> </td>

  </tr>
</table>
<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal">Mit geschickt platzierten Faktorpunkten kann man also sehr 
schnell viele Punkte erreichen.</p>

<?
// Datum der spätestens Tippabgabe berechnen
$date_weltmeister_tipp = $ausgabe["Meister_Tipp_Date"];
$help = preg_split("/-/", $date_weltmeister_tipp);
$date_weltmeister_tipp = $help[2].".".$help[1].". ". $help[0];

$time_weltmeister_tipp = $ausgabe["Meister_Tipp_Time"];
$help = preg_split("/:/", $time_weltmeister_tipp);
$date_weltmeister_tipp .= ", ". $help[0]. ":". $help[1]. " Uhr";
?>

<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal" align="center"><u><b><font size="5"><?php print("$ausgabe[Meistername]");?>-Tipp</font></b></u></p>
<p class="MsoNormal" align="center">&nbsp;</p>
<p class="MsoNormal">Ein erfolgreicher  <?php print("$ausgabe[Meistername]");?>-Tipp
					bringt Dir zus&auml;tzliche
					<b><?php print("$ausgabe[Meister_Tipp_Points] ");?></b>
					Punkte auf Dein Konto.<br>
					Beachte: Der Tipp muss bis spätestens zum Beginn des ersten Spiels nach der Vorrunde abgegeben werden, 
					also bis zum <b><?php print("$date_weltmeister_tipp");?></b>.</p>
<p>&nbsp;</p>

</body>

</html>
