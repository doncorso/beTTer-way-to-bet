<?php 
/*************************************************************
Hier wird am Ende des Turniers der turniersieger eingetragen
und unter dem user   ADMIN --> MEISTERTIPP  gespeichert
Anschliessend werden alle MeisterTipps aller user mit dem 
eingegebenen verglichen und dementsprechend Punkte verteilt

Selbstverstaendlich hat nur "admin" Zugriff auf diese Seite
*************************************************************/
require "general_methods.inc.php";
require "connect.inc.php";
check_session(true, array(), array("admin"));

// 'submit' gedrueckt?
$submit  =  $_POST["submit"];

/************
Anfrage zur Ermittlung des MEISTERNAMENS !
*************/
$dbanfrage = "SELECT Meistername, Turniername
        			FROM settings";                
$result  = mysql_db_query ($dbName, $dbanfrage, $connect);
$ausgabe = mysql_fetch_array ($result);
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<?php 
$head = create_head("Turniersieger eingeben"); 
print $head;
?>

<body>

<?php
$menu = create_menu();
print $menu;
?>

<form action="set_meister.php" method="post">

<?php
$heute = today();

//Wenn SUBMIT gedrueckt wurde und seite neu geladen wurde
if ($submit == " Absenden ") 
{
    /*************************
    Turniersieger saven
    **************************/
    $winner = $_POST[theWinner];
    $tippUpdate = "UPDATE user SET MeisterTipp=$winner WHERE user=\"$user\"";
    mysql_query($tippUpdate);
    
    // eingestellte Punkte für korrekten TurniersiegerTipp aus den Settings holen
    $dbanfrage = "SELECT Meister_Tipp_Points
            			FROM settings";                
    $result  = mysql_db_query ($dbName, $dbanfrage, $connect);
    $ausgabe_pnts = mysql_fetch_array ($result);
    $MeisterPunkte=$ausgabe_pnts[Meister_Tipp_Points];
    

    
    /*****************************************
    Admin - MEISTERTIPP mit den Tipps der user vergleichen
    und dementsprechend Punkte verteilen
    *****************************************/
        
    $db_getMTipps = mysql_query("SELECT USER_ID, user, MeisterTipp FROM user WHERE user != 'admin'");
    while ($ausgabeMTipps = mysql_fetch_array ($db_getMTipps) )
    {
        //Wenn MeisterTipp korrekt: Bonus speichern und auf Gesamtpunkte addieren
        if ($ausgabeMTipps[MeisterTipp] == $winner && $ausgabeMTipps[user]!= "admin")
        {                               
            $Bonus = $MeisterPunkte;
        }
        //Wenn MeisterTipp falsch
        else
        {
            $Bonus = 0;
        }
        
        $Meister_Points_Update = "UPDATE user SET MeisterTippPunkte = $Bonus WHERE USER_ID=$ausgabeMTipps[USER_ID]";
        mysql_query($Meister_Points_Update);

        //Jetzt noch die neuen Gesamtpunkte ermitteln;
        /************************************************************************************
        Die Summe der Spielpunkte plus MeisterTippPunkte in Gesamtpunkte des Users speichern
        *************************************************************************************/

        //TotalPoints werden BERECHNET (Summe der SpielPunkteSpalte des aktuellen users !//
        $calc_sum=("SELECT SUM(SpielPunkte) as total FROM tipp WHERE USER_ID=$ausgabeMTipps[USER_ID]");
        $calc_pointsum=mysql_query($calc_sum);
        $data = mysql_fetch_array($calc_pointsum);
        $totalpoints =  $data[total];


        //MeisterTippPunkte holen und draufaddieren (waehrend dem Turnier immer = 0)
        $MeisterPoints=("SELECT MeisterTippPunkte FROM user WHERE USER_ID=$ausgabeMTipps[USER_ID]");
        $get_MeisterPoints=mysql_query($MeisterPoints);
        $data = mysql_fetch_array($get_MeisterPoints);

        $totalpoints = $totalpoints + $data[MeisterTippPunkte];

        // neue Variante: Gesamtpunkte immer neu SETTEN//
            $Total_Points_Update = "UPDATE user SET TotalPoints=$totalpoints WHERE USER_ID=$ausgabeMTipps[USER_ID]";
            mysql_query($Total_Points_Update);
            
    }// End Of WHILE



    print("<table width=\"50%\" bgcolor=\"#000000\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" align=\"center\">");
    print ("<th bgcolor=\"#e7e7e7\" align=\"center\" colspan=\"0\">
           $user, Der $ausgabe[Meistername] wurde gespeichert.<br>Die Bonus-Punkte an die User wurden verteilt!<br>");
    print("$ausgabe[Turniername] ist VORBEI!<br></th>");
    print("</table>");
    print("<br><hr><br><br>");

}
//Wenn nicht SUBMIT gedrueckt wurde (erster Seitenaufruf)
else
{
    
    $count = 0;
    //Hole alle Spiele, die (datumsbezogen) noch in der Zukunft liegen    
    $db_get_games="SELECT SPIEL_ID
                   FROM spiel  WHERE DATE_FORMAT( Datum,  '%Y-%m-%d'  )  > \"$heute\"";
    $gamescount = mysql_query($db_get_games);
    
    //zaehle alle geholten SPiele
    while ($ausgabe_get_gameID = mysql_fetch_array($gamescount))
    {
        $count++;
    }
    
    //print("--> Anzahl Spiele, die tagesbezogen in der Zukunft liegen: $count");
    
    //Wenn keine SPiele mehr in der Zukunft
    if ($count == 0)
    {    

        $db_getTeams = "SELECT TEAM_ID, Name FROM team ORDER BY Name";
        $resultT = mysql_query($db_getTeams);

        print ("</SELECT>");

        print ("<p align=\"center\">$ausgabe[Meistername] ist &nbsp<SELECT NAME='theWinner'>");

        while ($ausgabeTeams = mysql_fetch_array ($resultT) )
        {
            print ("<OPTION VALUE='$ausgabeTeams[TEAM_ID]'>$ausgabeTeams[Name]");
        }
        print ("</p> </SELECT>&nbsp geworden");
        print("<br><br><br>");
        print('<input type="submit" name="submit" value=" Absenden ">
              </form>');
    }
    else
    {
        print("<h3 align=\"center\"> Das Turnier läuft noch... (noch <font color=\"#990000\">$count</font> Spiele stehen an)<br> 
               Der $ausgabe[Meistername] steht noch nicht fest. </h3>");
    }
}

?>

</body>
</html>

