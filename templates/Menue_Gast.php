<?php
// Menü-Template generieren
$menu  = '<table width="400" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">';
$menu .= '<tr>';
$menu .= '<td bgcolor="#e7e7e7" align="center" colspan="3">';
$menu .= '<b>Hallo '. $_SESSION["user"]. '</b>';
$menu .= '</tr>';
$menu .= '</tr>';
$menu .= '<tr>';
$menu .= '<td width="400" align="center" valign="top">';
$menu .= '<b><u> %KAT% </u></b>';
$menu .= '%ENTRIES%';
$menu .= '</td>';
$menu .= '</tr>';
$menu .= '</table>';
?>