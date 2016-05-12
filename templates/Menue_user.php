<?php
// Menü-Template generieren
$menu  = '<table width="750" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">';
$menu .= '<tr>';
$menu .= '<td bgcolor="#e7e7e7" align="center" colspan="3">';
$menu .= '<b>Hallo '. $_SESSION["user"]. '</b>';
$menu .= '</tr>';
$menu .= '</tr>';
$menu .= '<tr>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_LEFT% </u></b>';
$menu .= '%ENTRIES_LEFT%';
$menu .= '</td>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_CENTER% </u></b>';
$menu .= '%ENTRIES_CENTER%';
$menu .= '</td>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_RIGHT% </u></b>';
$menu .= '%ENTRIES_RIGHT%';
$menu .= '</td>';
$menu .= '</tr>';
$menu .= '</table>';
?>