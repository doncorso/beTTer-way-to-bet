<?php
// FIXME!!!
//  IDEA:
//    Create all pages with a template like the one below.
//    TODO: Check how this is possible!
//          Check if this is really necessary (now that we have already methods creating the pages very similarly).
//
//Parts:
//-------------------

//1) SESSION_START
//---------------------
//Bsp.:
//session_start(); 
//if (!isset($_SESSION['user']) || $_SESSION['user'] != "admin") {
//  header("location:index.php"); 
//  die; 
//}

//2) HTML
//---------------------
//Bsp.:
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
//<html>
//  %HEAD%
//  %BODY%
//</html>

//3) HEAD
//---------------------
//Bsp.:
//<head>
//  <title>%TITLE%</title>
//  <link rel="stylesheet" type="text/css" href="style.css">
//</head>

//4) BODY
//---------------------
//Bsp.:
//<body>
//  %MENU% // to create via create_menu()
//  %CONTENT% // 'normal' site content (e.g. form to upload files in file_upload.php etc.)
//</body>
?>