<?php 
// -------------------------------------------------------------------------------------------
//	Die aktuelle Versionsnummer.
//  Wird benutzt in index_2.php.
// -------------------------------------------------------------------------------------------
define ("VERSION", "0.9.12");

// -------------------------------------------------------------------------------------------
//	Min. und Max. der vergebbaren Tore (beeinflusst u.a. die Auswahllisten in tab.php).
// -------------------------------------------------------------------------------------------
define ("MIN_TORE",  0);
define ("MAX_TORE", 19);

// -------------------------------------------------------------------------------------------
//	Standard-Anzeigegroesse der Flaggen (unabhaengig von der Groesse der Datei).
// -------------------------------------------------------------------------------------------
define ("FLAG_WIDTH" , 40);
define ("FLAG_HEIGHT", 24);

// -------------------------------------------------------------------------------------------
//	Illegale Zeichen fuer Benutzernamen. 
//  Benutzt bei index_2.php und mitmachen.php.
//  Achtung: 
//    - Wenn dies als array benutzt werden soll, dann mit str_split() splitten.
// -------------------------------------------------------------------------------------------
define ("ILLEGAL_CHARS", "-*;'+& ");

// -------------------------------------------------------------------------------------------
//	Debugging-Kram.
//  Wenn mehrere DEBUG_USERS angegeben werden sollen: mit Komma trennen!
// -------------------------------------------------------------------------------------------
define ("DEBUG" , true);
define ("DEBUG_USERS", "Manolo");
?>