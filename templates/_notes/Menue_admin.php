<?php
// Menü-Template generieren
$menu  = '<table width="1000" bgcolor="#000000" border="0" cellpadding="5" cellspacing="1" align="center">';
$menu .= '<tr>';
$menu .= '<td bgcolor="#e7e7e7" align="center" colspan="4">';
$menu .= '<b>Hallo '. $_SESSION["user"]. '</b>';
$menu .= '</tr>';
$menu .= '</tr>';
$menu .= '<tr>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_ONE% </u></b>';
$menu .= '%ENTRIES_ONE%';
$menu .= '</td>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_TWO% </u></b>';
$menu .= '%ENTRIES_TWO%';
$menu .= '</td>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_THREE% </u></b>';
$menu .= '%ENTRIES_THREE%';
$menu .= '</td>';
$menu .= '<td width="250" align="center" valign="top">';
$menu .= '<b><u> %KAT_FOUR% </u></b>';
$menu .= '%ENTRIES_FOUR%';
$menu .= '</td>';
$menu .= '</tr>';
$menu .= '</table>';
?>