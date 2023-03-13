<?php
    // This initial file contain all the roots that we will use in our backend webpage
    require 'connect.php'; // This will link to the connect file of our database
    // Roots
    $tpl = 'include/templates/'; // Template derictory
    $func = 'include/functions/'; // function derictory
    $css = 'Themes/CSS/'; // CSS derictory
    $js = 'Themes/JS/'; // JS derictory
    $img = 'Themes/IMAGES/'; // IMAGES derictory

    // Include important links
    include $func . 'function.php'; // Include the function file
    include $tpl . 'header.php'; // Include our header file

    // INCLUDE THE NAVBAR TO ALL PAGES EXCCEPT THAT HAS THE VARIABALE noNavbar
    if(!isset($noNavbar)) { include $tpl . 'navbar.php'; }; // Sometimes we do not need the navbar like in login