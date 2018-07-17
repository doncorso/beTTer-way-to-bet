<?php 
require_once "general_defs.inc.php";

// -------------------------------------------------------------------------------------------
//	email_header()
//	Gibt den Standard-Header für Emails zurück.
//
//	Parameter:
//	$sender : Die Email-Adresse des Senders
//
//  ACHTUNG:
//    Der Text wird UTF8-codiert.
//    Umlaute funktionieren daher immer, wenn man sie einfach in den Text reinschreibt.
//    Umlaute, die in VARIABLEN gespeichert sind, sollten vorher mit utf8_encode() kodiert werden!
// -------------------------------------------------------------------------------------------
function email_header($sender) {
  $headers   = array();
  $headers[] = "MIME-Version: 1.0";
  $headers[] = "Content-type: text/plain; charset=utf-8";
  $headers[] = "From: {$sender}";
  return implode("\r\n", $headers);
}

// -------------------------------------------------------------------------------------------
//	today()
//	Gibt das heutige Datum (im richtigen Format) zurück.
// -------------------------------------------------------------------------------------------
function today() {
	return date("Y-m-d");
	//return "2014-06-14"; // FIXME!!! Test!!!
}

// -------------------------------------------------------------------------------------------
//	now()
//	Gibt die jetzige Uhrzeit (im richtigen Format) zurück.
// -------------------------------------------------------------------------------------------
function now() {
	return date("H:i:s");
	//return "23:00:00"; // FIXME!!! Test!!!
}

// -------------------------------------------------------------------------------------------
//	timestamp_to_date()
//	Bringt ein Timestamp-Datum in normales Anzeigeformat.
//
//	Parameter:
//	$timestamp : Die zu konvertierende Angabe.
//
//	ACHTUNG:  !!! Keine Prüfung auf falsche Daten !!!
// -------------------------------------------------------------------------------------------
function timestamp_to_date($timestamp) {
	return 	substr($timestamp, 6, 2). ".". 
			substr($timestamp, 4, 2). ". ".
			substr($timestamp, 0, 4). " ".
			substr($timestamp, 8, 2). ":".
			substr($timestamp,10, 2);
}

// -------------------------------------------------------------------------------------------
//	date_to_timestamp()
//	Konvertiert ein Datum in ein Timestamp-Datum.
//
//	Parameter:
//	$datum: Die zu konvertierende Angabe.
//          Erwartetes Format so wie aus der Datenbank: YYYY-MM-DD HH::MM::SS
//
//	ACHTUNG:  !!! Keine Prüfung auf falsche Daten !!!
// -------------------------------------------------------------------------------------------
function date_to_timestamp($date) {
	$dateArr = array();
	preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2})\:(\d{2})\:(\d{2})/',$date, $dateArr);
	array_shift($dateArr); // remove first element (alias the full matched string)
	return join("", $dateArr);
}


// -------------------------------------------------------------------------------------------
//	wday()
//	Gibt den deutschen Wochentag abgekürzt zurück.
//
//	Parameter:
//	$timestamp : Der Wochentag, wie er z.B. aus der date()- Funktion kommt, als Zahl.
// -------------------------------------------------------------------------------------------
function wday($timestamp) {

	$time = mktime(	0,
					0,
					0, 
					substr($timestamp, 4, 2),
					substr($timestamp, 6, 2),
					substr($timestamp, 0, 4));
	$wday = date("w", $time);

	switch($wday) {
		case 0: return "SO";
		case 1: return "MO";
		case 2: return "DI";
		case 3: return "MI";
		case 4: return "DO";
		case 5: return "FR";
		case 6: return "SA";						
		}
	return "??";
}

// -------------------------------------------------------------------------------------------
//	timestamp_to_full_date()
//	Bringt ein Timestamp-Datum in ein erweitertes Anzeigeformat.
//
//	Parameter:
//	$timestamp : Die zu konvertierende Angabe.
//
//	ACHTUNG:  !!! Keine Prüfung auf falsche Daten !!!
// -------------------------------------------------------------------------------------------
function timestamp_to_full_date($timestamp) {
  $date = timestamp_to_date($timestamp);
  $date = str_replace(" ", ", ", $date);
  $date = str_replace("., ", ". ", $date);
  $wday = wday($timestamp);
  $full_date = $wday. ", den ". $date. " Uhr";
  return $full_date;
}

// -------------------------------------------------------------------------------------------
//	date_to_full_date()
//	Bringt ein Datum in ein erweitertes Anzeigeformat.
//
//	Parameter:
//	$date : Die zu konvertierende Angabe.
//
//	ACHTUNG:  !!! Keine Prüfung auf falsche Daten !!!
// -------------------------------------------------------------------------------------------
function date_to_full_date($date) {
  $timestamp  = date_to_timestamp($date);
  $full_date  = timestamp_to_full_date($timestamp);
  return $full_date;
}

// -------------------------------------------------------------------------------------------
//	date_time_to_full_date()
//	Bringt ein Datum in ein erweitertes Anzeigeformat.
//  Benutzung:
//    Z.B. aus der Datenbank "Datum" und "Anpfiff" auslesen und hier übergeben.
//
//	Parameter:
//	$date : das zu konvertierende Datum   (z.B. "Datum")
//	$time : die zu konvertierende Uhrzeit (z.B. "Anpfiff")
//
//	ACHTUNG:  !!! Keine Prüfung auf falsche Daten !!!
// -------------------------------------------------------------------------------------------
function date_time_to_full_date($date, $time) {
  $str        = $date. " ". $time; 
  $timestamp  = date_to_timestamp($str);
  $full_date  = timestamp_to_full_date($timestamp);
  return $full_date;
}

// -------------------------------------------------------------------------------------------
//	split_timestamp
//	Gibt ein Array zurück, das die einzelnen timestamp-Komponenenten enthält, und zwar in 
//  der Sequenz: (Y,m,d,H,i,s).
//
//	Parameter:
//	$timestamp : der zu splittende Timestamp (Annahme: Format = "YmdHis"!!!)
// -------------------------------------------------------------------------------------------
function split_timestamp($timestamp) {
	$dateArr = array();
	preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/',$timestamp, $dateArr);
	array_shift($dateArr); // remove first element (alias the full matched string)

  // Gib ein assoziatives Array zurück (leichter nachzubehandeln)	
	$result = array();
	$result["Y"] = $dateArr[0];
	$result["m"] = $dateArr[1];
  $result["d"] = $dateArr[2];	
	$result["H"] = $dateArr[3];
	$result["i"] = $dateArr[4];
	$result["s"] = $dateArr[5];			
	return $result;
}

// -------------------------------------------------------------------------------------------
//	add_date
//	Addiert so viele Jahre, Monate, Tage, Stunden, Minuten und Sekunden wie angegeben.
//
//	Parameter:
//	$origDate : das Ausgangsdatum als timestamp im Format "YmdHis"
//  $Y        : die zu addierenden Jahre
//  $m        : die zu addierenden Monate
//  $d        : die zu addierenden Tage
//  $H        : die zu addierenden Stunden
//  $i        : die zu addierenden Minuten
//  $s        : die zu addierenden Sekunden
// -------------------------------------------------------------------------------------------
function add_date($origDate, $Y, $m, $d, $H, $i, $s) {
	$arr = split_timestamp($origDate);
  return date("YmdHis", mktime($arr["H"]+$H, $arr["i"]+$i, $arr["s"]+$s, $arr["m"]+$m, $arr["d"]+$d, $arr["Y"]+$Y));
}

// -------------------------------------------------------------------------------------------
//	add_dates
//	Addiert zwei Daten. Beide müssen in der Form "YmdHis" sein.
//  Vereinfacht ggf. die Addition eins Datums, weil man "YmdHis" direkt als String angeben 
//  kann und nicht die Parameter einzeln braucht.
//
//	Parameter:
//	$dateOne : Datum 1
//  $dateTwo : Datum 2
// -------------------------------------------------------------------------------------------
function add_dates($dateOne, $dateTwo) {
	$arrOne = split_timestamp($dateOne);
  $arrTwo = split_timestamp($dateTwo);
  return date("YmdHis", 
	            mktime($arrOne["H"]+$arrTwo["H"], 
							       $arrOne["i"]+$arrTwo["i"], 
										 $arrOne["s"]+$arrTwo["s"], 
										 $arrOne["m"]+$arrTwo["m"], 
										 $arrOne["d"]+$arrTwo["d"], 
										 $arrOne["Y"]+$arrTwo["Y"]));
}

// -------------------------------------------------------------------------------------------
//	dump
//	Gibt eine Variable via var_dump aus, und zwar in einem schön formatierten Kontext,
//  der mittels "<pre>...</pre>" erzeugt wird.
//
//	Parameter:
//	$data: Die zu dumpende Variable. Sie kann alles sein, da var_dump() alle Parametertypen 
//          akzeptiert (z.B. arrays etc.)
// -------------------------------------------------------------------------------------------
function dump($data) {
  echo "<pre>";
	var_dump($data);
	echo "</pre>";
}


// -------------------------------------------------------------------------------------------
//	singplur
//	Gibt einen String im Singular oder Plural aus.
//  Bsp.:
//     singplur("wurde", "wurden", 1) => "wurde"
//     singplur("wurde", "wurden", 2) => "wurden"
//     singplur("Faktorpunkt", "Faktorpunkte", 1) => "Faktorpunkt"
//     singplur("Faktorpunkt", "Faktorpunkte", 2) => "Faktorpunkte"
//    
//	Parameter:
//	$sing : Text im Singular.
//  $plur : Text im Plural.
//  $num  : Anzahl (= ggf. Auslöser für Plural)
// -------------------------------------------------------------------------------------------
function singplur($sing, $plur, $num) {
	if ($num == 1) return $sing;
	return $plur;
}

// -------------------------------------------------------------------------------------------
//	correctTor
//	Gibt den "echten" Torwert aus, d.h. initial 0 bzw. $newDefault, sonst höchstens $max oder $min.
//    
//	Parameter:
//	$tor     : Die Anzahl Tore, die korrigiert werden soll.
//  $min     : Die minimal erlaubte Anzahl Tore.
//  $max     : Die maximal erlaubte Anzahl Tore.
//  $default : Wenn $tor == 99, also initial, dann kann hier ein neuer Initialwert vergeben werden.
//             Wenn nicht angegeben, wird $default = 0 angenommen.
// -------------------------------------------------------------------------------------------
function correctTor($tor, $min, $max, $default=0) {
  //print "tor (min/max) = $tor ($min/$max)<br>";

	if ($tor == 99)  return $default; // 99 = = initialer, automatischer Tipp beim Anlegen des users. Wird übersetzt zu $default.
  if ($tor < $min) return $min;     // Mindestens $min zurückgeben
	if ($tor > $max) return $max;     // Höchstens $max zurückgeben
	return $tor;
}

// -------------------------------------------------------------------------------------------
//	check_login_name
//	Prueft, ob ein Login-Name nur gueltige Zeichen enthaelt.
//  Gibt einen entsprechenden Fehler-String zurueck, falls ja.
//    
//	Parameter:
//	$name         : Der Login-Benutzernname.
//  $illegalChars : Die Zeichen, die verboten werden sollen, als string.
// -------------------------------------------------------------------------------------------
function check_login_name($loginName, $illegalCharsStr) {

  $illegalChars = str_split($illegalCharsStr);
  $usedIllegalChars = array();
  
  foreach ($illegalChars as $i) {
    if (stripos($loginName, $i) !== false) array_push($usedIllegalChars, $i); 
  }
  
  if (count($usedIllegalChars) > 0) {
    $err = "Ung&uuml;tige";
    if (count($usedIllegalChars) == 1) $err .= "s";
    $err .= " Zeichen benutzt: \"";
    $err .= implode ("\", \"", $usedIllegalChars);
    $err .= "\".";
    return $err;
  }
  
  return true;
}

// -------------------------------------------------------------------------------------------
//	check_session
//	Prüft, ob sich nur diejenigen User auf dieser Seite befinden, die dort sein sollen.
//  Gibt außerdem einen String zurück, der dann hingeschrieben wird.
//    
//	Parameter:
//	$need_reg    : Muss man sich hier überhaupt einloggen?
//                 Wenn gesetzt, werden nur Eingeloggte zugelassen. 
//                 Kein Login --> index.php.
//  $users_reloc : Ein array von Benutzern, die auf eine andere Seite geleitet werden sollen.
//                 Wird ein solcher gefunden --> weiterleiten auf $user.php
//  $users_yes   : Ein array von zulässigen Benutzern. 
//                 Wird kein zulässiger gefunden --> index.php
//  $users_no    : Ein array von unzulässigen Benutzern.
//                 Wird ein solcher gefunden --> index.php
// -------------------------------------------------------------------------------------------
function check_session($need_reg, $users_reloc=array(), $users_yes=array(), $users_no=array()) {
  
	session_start();
  if (!$need_reg) return;
	
  $user = strtolower($_SESSION['user']);

  // nur Eingeloggte zulassen
  if(!isset($_SESSION['user']) || $user == "") {
    relocate("index.php");
  }

  // Weiterzuleitende user weiterleiten
  foreach ($users_reloc as $reloc) {
    if ($user == strtolower($reloc)) {
      relocate("$user.php");
    }
  }

  // Nur zulässige user akzeptieren
  $found_ok = false;
  foreach ($users_yes as $yes) {
    if ($user == strtolower($yes)) {
      $found_ok = true;
      break;
    }
  }
  
  if (count($users_yes) > 0 && $found_ok == false) {
    relocate("index.php");
  }

  // Unzulässige user verbieten
  foreach ($users_no as $no) {
    if ($user == strtolower($no)) {
	    relocate("index.php");
    }
  }
}

// -------------------------------------------------------------------------------------------
//	relocate
//	Wechselt zur angegebenen $location und terminiert.
//    
//	Parameter:
//	$location : Das Ziel.
// -------------------------------------------------------------------------------------------
function relocate($location) {
  header("location:$location"); 
  die;
}

// -------------------------------------------------------------------------------------------
//	create_head
//	Generiert den <head>...</head> - Text.
//    
//	Parameter:
//	$title  : Der Titel.
// -------------------------------------------------------------------------------------------
function create_head($title) {

  $str  = '<head>';
  $str .= '<title>'. $title. '</title>';
  $str .= '<link rel="stylesheet" type="text/css" href="style.css">';
  $str .= '</head>';
  return $str;
}

// -------------------------------------------------------------------------------------------
//	create_menu
//	Holt sich den String $menu aus der entsprechenden Datei und füllt die Einträge ein.
//    
//	Parameter:
//  $user     : Für wen das Menü erstellt werden soll. 
//              Default: "user", also alle normalen Spieler. 
//              Sonstige Möglichkeiten: admin, Gast.
//	$entries  : Die Einträge der jeweiligen Kategorie
//	$kats     : Die Kategorien.
// -------------------------------------------------------------------------------------------
function create_menu($user="", $entries=array(), $kats=array()) {

  if ($user == "") $user = user_to_menu_user($_SESSION["user"]);

  require("./templates/Menue_$user.php");
  $res = str_replace("%ACTION%", $action, $menu); 

	if (count($entries) == 0) {
		$entries = get_menu_entries($user);
	}
	
  if (count($kats) == 0) {
  	$kats = get_menu_kats($user);
	}

  if ($entries && count($entries) > 0) {
    $res = add_menu_entries($user, $res, $entries, $kats);
  }

	$res .= "<br><br>";  
  return $res;
}

// -------------------------------------------------------------------------------------------
//	user_to_menu_user
//	Übersetzt den aktuellen User in den menuUser, d.h. den User, der für das Menü angenommen wird.
//    
//	Parameter:
//  $user     : der zu übersetzende user.
// -------------------------------------------------------------------------------------------
function user_to_menu_user($user) {
  if (strtolower($user) == "gast")  return "Gast";
  if (strtolower($user) == "admin") return "admin";
  return "user"; // all other users are default, which is simply called "user"
}


// -------------------------------------------------------------------------------------------
//	get_menu_entries
//	Holt sich das $entries-array in Abhängigkeit vom user.
//    
//	Parameter:
//  $user     : Für wen die Menüpunkte ausgelesen werden sollen. 
// -------------------------------------------------------------------------------------------
function get_menu_entries($user) {

	if ($user == "user") {
		return array( array("href" => "sichere_seite.php"    , "text" => "Hauptmen&uuml;",            "kat" => "ALLGEMEIN")     ,
									array("href" => "mylogin.php"          , "text" => "Meine Daten",               "kat" => "ALLGEMEIN")     ,
                  array("href" => "tipphelp.php"         , "text" => "Hilfe zum Tippen",          "kat" => "ALLGEMEIN")     ,
                  array("href" => "logout.php"           , "text" => "Logout",                    "kat" => "ALLGEMEIN")     ,
                  array("href" => "tab.php"              , "text" => "Meine Tipps",               "kat" => "TIPPEN"         , "no" => array("admin","Gast")),
                  array("href" => "meistertipp.php"      , "text" => "Mein Turniersieger-Tipp",   "kat" => "TIPPEN"         , "no" => array("admin","Gast")),
                  array("href" => "ranking.php"          , "text" => "Aktuelle Rangliste",        "kat" => "TIPPEN")        ,
                  array("href" => "gewinnverteilung.php" , "text" => "Aktuelle Gewinnverteilung", "kat" => "TIPPEN")        ,
                  array("href" => "view_gametipps.php"   , "text" => "Die Spiele-Tipps",          "kat" => "DIE MITSPIELER"),
                  array("href" => "view_meistertipps.php", "text" => "Die Turniersieger-Tipps",   "kat" => "DIE MITSPIELER"),
                  array("href" => "mail_user.php"        , "text" => "Mail schreiben an...",      "kat" => "DIE MITSPIELER"),
                );
	}

	if ($user == "admin") {
		return array( array("href" => "admin.php"                , "text" => "Hauptmen&uuml;",                 "kat" => "ALLGEMEIN"),
									array("href" => "mylogin.php"              , "text" => "Meine Daten",                    "kat" => "ALLGEMEIN"),
									array("href" => "admin_settings.php"       , "text" => "Einstellungen zum Turnier",      "kat" => "ALLGEMEIN"),
                  array("href" => "set_meister.php"          , "text" => "Turniersieger eingeben",         "kat" => "ALLGEMEIN"),
                  array("href" => "tipphelp.php"             , "text" => "Hilfe zum Tippen",               "kat" => "ALLGEMEIN"),
									array("href" => "ranking.php"              , "text" => "Aktuelle Rangliste",             "kat" => "ALLGEMEIN"),
                  array("href" => "gewinnverteilung.php"     , "text" => "Aktuelle Gewinnverteilung",      "kat" => "ALLGEMEIN"),									
                  array("href" => "logout.php"               , "text" => "Logout",                         "kat" => "ALLGEMEIN"),
                  array("href" => "file_upload.php"          , "text" => "Flaggen hochladen",              "kat" => "TEAM")     ,
                  array("href" => "team_upload_by_file.php"  , "text" => "Teams aus Datei einlesen",       "kat" => "TEAM")     ,
                  array("href" => "new_team.php"             , "text" => "Team anlegen",                   "kat" => "TEAM")     ,
                  array("href" => "ren_team.php"             , "text" => "Team umbenennen",                "kat" => "TEAM")     ,
                  array("href" => "del_team.php"             , "text" => "Team l&ouml;schen",              "kat" => "TEAM")     ,
                  array("href" => "new_flag.php"             , "text" => "Flagge Team zuordnen",           "kat" => "TEAM")     ,
                  array("href" => "neu.php"                  , "text" => "Neuen User anlegen",             "kat" => "USER")     ,
                  array("href" => "del_user.php"             , "text" => "User l&ouml;schen",              "kat" => "USER")     ,
                  array("href" => "init_user_tipps.php"      , "text" => "User-Tipps zur&uuml;cksetzen",   "kat" => "USER")     ,
                  array("href" => "mail_to_all_admin.php"    , "text" => "Allen User mailen",              "kat" => "USER")     ,
                  array("href" => "spiele_upload_by_file.php", "text" => "Spiele aus Datei einlesen",      "kat" => "SPIEL")    ,
                  array("href" => "new_kategorie.php"        , "text" => "Kategorie anlegen",              "kat" => "SPIEL")    ,
                  array("href" => "ren_kategorie.php"        , "text" => "Kategorie umbenennen",           "kat" => "SPIEL")    ,
                  array("href" => "new_game.php"             , "text" => "Spiel anlegen",                  "kat" => "SPIEL")    ,
                  array("href" => "chg_game.php"             , "text" => "Spiel &auml;ndern",              "kat" => "SPIEL")    ,
                  array("href" => "del_game.php"             , "text" => "Spiel l&ouml;schen",             "kat" => "SPIEL")    ,
                  array("href" => "set_result.php"           , "text" => "Spielergebnis eingeben",         "kat" => "SPIEL")    ,
                );
	}
	
	if ($user == "Gast") {
		return array( array("href" => "gast.php"             , "text" => "Hauptmen&uuml;",            "kat" => "ALLGEMEIN"),
									array("href" => "mitmachen.php"        , "text" => "Ich will mitmachen",        "kat" => "ALLGEMEIN"),									
                  array("href" => "tipphelp.php"         , "text" => "Hilfe zum Tippen",          "kat" => "ALLGEMEIN"),
	 							  array("href" => "ranking.php"          , "text" => "Aktuelle Rangliste",        "kat" => "ALLGEMEIN"),									
                  array("href" => "logout.php"           , "text" => "Logout",                    "kat" => "ALLGEMEIN"),
                );
	}

  print "Illegaler Nutzer: \"$user\"! Men&uuml;punkte nicht generiert!<br>";
	return array();
}

// -------------------------------------------------------------------------------------------
//	get_menu_kats
//	Holt sich das $kats-array in Abhängigkeit vom user.
//    
//	Parameter:
//  $user     : Für wen die Kategorien ausgelesen werden sollen. 
// -------------------------------------------------------------------------------------------
function get_menu_kats($user) {

	if ($user == "user") {
		return array ("ALLGEMEIN", "TIPPEN", "DIE MITSPIELER");
	}

	if ($user == "admin") {
		return array ("ALLGEMEIN", "TEAM", "USER", "SPIEL");
	}
	
	if ($user == "Gast") {
		return array ("ALLGEMEIN");
	}

  print "Illegaler Nutzer: \"$user\"! Men&uuml;-Kategorien nicht generiert!<br>";
	return array();
}

// -------------------------------------------------------------------------------------------
//	get_replacement_string
//	Holt sich den zu ersetzenden string in Abhängigkeit des User-Templates.
//  Beispiel:
//    Für Gast wird ein Leerstring 
//    
//	Parameter:
//  $user : Welcher user alias welches Template benutzt werden soll.
// -------------------------------------------------------------------------------------------
function get_replacement_string($user, $kat, $num) {
  if ($user == "Gast") {
	  if ($kat) return "%KAT%";
		else      return "%ENTRIES%";
	}
	
	$res = "%";
	if ($kat) { $res .= "KAT_"; }
	else      { $res .= "ENTRIES_"; }
	
	if ($user == "user") {
    switch ($num) {
      case 0: $res .= "LEFT"; break;
			case 1: $res .= "CENTER"; break;
			case 2: $res .= "RIGHT"; break;
		}
		
		$res .= "%";
		return $res;
	} 
	
	if ($user == "admin") {
    switch ($num) {
      case 0: $res .= "ONE"; break;
			case 1: $res .= "TWO"; break;
			case 2: $res .= "THREE"; break;
			case 3: $res .= "FOUR"; break;
		}
		
		$res .= "%";
		return $res;
	} 
	
  print "Illegale Parameter: user / kat / num = \"$user\" / \"$kat\" / \"$num\". Kein replacement_string generiert!<br>";
	return "";
}


// -------------------------------------------------------------------------------------------
//	add_menu_entries
//	Ersetzt den Text '%ENTRIES_EFT%' bzw. '%ENTRIES_RIGHT' im entsprechenden string $menu 
//   mit den übergebenen Parametern.
//    
//	Parameter:
//	$menu    : Der bisherige Menüstring.
//  $entries : Ein array von Menüeinträgen.
//  $kats    : 
// -------------------------------------------------------------------------------------------
function add_menu_entries($menuUser, $menu, $entries, $kats) {

	$res = $menu;
  $myself = myself();
  //print "myself = $myself<br>";
  $user = $_SESSION["user"];

  $katCnt	= 0;
	foreach ($kats as $kat) {

	  $str = "";
	  $first = true;
		foreach ($entries as $key => $value) {
	
      if ($first) {
			  // ersetze z.B. %KAT_ONE% für den Admin mit $katCnt = 0 etc.
				$res = str_replace(get_replacement_string($menuUser, true, $katCnt), $kat, $res);

//			  if ($katCnt == 0) {
//          $res = str_replace("%KAT_LEFT%", $kat, $res);
//				} else if ($katCnt == 1) {
//				  $res = str_replace("%KAT_CENTER%", $kat, $res);
//				} else {
//				  $res = str_replace("%KAT_RIGHT%", $kat, $res);
//				}
			  $first = false;
			}
	
	    // handle only entries for current $kat
			if ($value["kat"] != $kat) continue;

			// ignore "no"-users
			$noUsers = $value["no"];
			if (count($noUsers) > 0) { 
  			$foundNoUser = false;
  			foreach ($value["no"] as $noUser) {
  			  if ($user == $noUser) {
  			    $foundNoUser = true;
  			    break;
  			  } 
  			}
  			if ($foundNoUser) continue;
			}
	
			$str .= '<br><br>';
			if ($myself == $value["href"]) {
				$str .= '<b>--> '. $value["text"]. ' <--</b>';
			} else {
				$str .= '<a href="'. $value["href"]. '">'. $value["text"]. '</a>';
			}
		}

//		if ($katCnt == 0) {
//			$res = str_replace("%ENTRIES_LEFT%", $str, $res);
//		} else if ($katCnt == 1) {
//  		$res = str_replace("%ENTRIES_CENTER%", $str, $res);
//		} else {
//  		$res = str_replace("%ENTRIES_RIGHT%", $str, $res);
//		}

		// ersetze z.B. %ENTRIES_ONE% für den Admin mit $katCnt = 0 etc.
		$res = str_replace(get_replacement_string($menuUser, false, $katCnt), $str, $res);

		$katCnt++;
	}
  
	return $res;
}


// -------------------------------------------------------------------------------------------
//	myself
//	Gibt den Namen des aktuell aufgerufenen Skripts zurueck (ohne Pfad).
// -------------------------------------------------------------------------------------------
function myself() {

  $myself = $_SERVER["PHP_SELF"];
  //print "self = $self<br>";
  
  // Zerlege $self in Verzeichnisbaum
  $arr = preg_split("/[\/]+/", $myself);
  //print_r($arr);
  
  // myself = letztes Array-Element
  $myself = $arr[count($arr)-1];
  
  return $myself;
}

// -------------------------------------------------------------------------------------------
//	root_url
//	Gibt den Namen des roots der Seite zurueck.
// -------------------------------------------------------------------------------------------
function root_url() {
  $url = $_SERVER['SCRIPT_URI'];
  $weg = strrchr($url,"/"); // alles nach letztem "/" loeschen (also eigene PHP-Datei), damit auf index verwiesen wird
  $url = str_replace($weg,"",$url);
	return $url;
}

// -------------------------------------------------------------------------------------------
//	array_join
//	Gibt die Werte eines arrays mit dem angegebenen $delimiter aus.
// -------------------------------------------------------------------------------------------
function array_join($array, $delimiter) {

  $str = "";
	while($res = array_shift($array)) {
		$str .= $res. $delimiter;
	}
	
	return $str;
}

// -------------------------------------------------------------------------------------------
//	debug()
//	Gibt zurueck, ob das Skript sich gerade im Debug-Modus befindet (d.h. Testausgaben
//  schreiben soll) oder nicht.
//  Achtung: 
//     - Die Kuer waere, Debug Levels einzufuehren. Dazu muessten aber viele Ausgaben
//       quer durch das gesamte better ersetzt werden.      
// -------------------------------------------------------------------------------------------
function debug() {

	$debug_users = preg_split('/[\s,]+/', DEBUG_USERS, -1, PREG_SPLIT_NO_EMPTY);
	if (DEBUG && 
	    $_SESSION["user"] && 
			in_array($_SESSION["user"], $debug_users)) return true;

	return false;
}

// -------------------------------------------------------------------------------------------
//	rfpAsString()
//	Gibt die restlichen Faktorpunkte (inkl. Durchschnitt pro Spiel) als String zurueck.
// -------------------------------------------------------------------------------------------
function rfpAsString($restFaktor, $avgFaktor) {

  $res = "Restliche Faktor-Punkte: $restFaktor (&#216; ";
  $res .= sprintf("%.2f", $avgFaktor);
  $res .= ")";
  return $res;
}


/*
// Activate for testing!
$a = "2012-06-08";
$b = "18:00";
$date = $a. " ". $b. ":00";
$timestamp = date_to_timestamp($date);
print "timestamp = $timestamp<BR>";
print timestamp_to_full_date($timestamp). "<br>";

$a = "2012-06-08";
$b = "18:00:00";
print date_time_to_full_date($a, $b). "<br>";

// ----------------------------------------------------------------------------------------------

$entries = array( 0 => array("href" => "mylogin.php", "text" => "Meine Daten"),
                  1 => array("href" => "tab.php"    , "text" => "Meine Tipps"),
                );
$menu = "%ENTRIES%";
$res = add_menu_entries($menu, $entries);
print "$res<br>";

// ----------------------------------------------------------------------------------------------

$arr = array("Eppel", "Baneeensche", "Oroooaaasch");
echo array_join($arr, "<br>-------------<br>");
*/
?>