<?php
session_start() ;
/* 
 * Load the basic global settings.
 */

// Load settings from ini file.
$ini = parse_ini_file('config.ini', true, INI_SCANNER_TYPED) ;
foreach ($ini as $key => $value) {
    $_SESSION[$key] = $value ;
}

// Set language for gettext
require_once $_SESSION['root_dir'] . "/lang.inc" ; 

// Setup error handler.
require_once $_SESSION['root_dir'] . '/classes/errors.php';
$errors = new errors() ;

// Setup database access
require_once $_SESSION['root_dir'] . '/libs/mysql/mysql.php' ;
