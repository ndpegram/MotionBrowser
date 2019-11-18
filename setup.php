<?php
/* 
 * Load the basic global settings.
 */

// Load settings from ini file.
$ini = parse_ini_file('config.ini', true, INI_SCANNER_TYPED) ;
foreach ($ini as $key => $value) {
    $_SESSION[$key] = $value ;
}

// TODO: Do some sanity checking e.g. disk free space must not be zero nor 100%. 
//       Perhaps create sanity check method which reports on load. Could also save date checked and compare with editing date of settings file so only checks if file changed since last check.

// Set language for gettext
require_once $_SESSION['root_dir'] . "/lang.inc" ; 

// Setup error handler.
require_once $_SESSION['root_dir'] . '/classes/errors.php';
$errors = new errors() ;

// Setup database access
require_once $_SESSION['root_dir'] . '/libs/mysql/mysql.php' ;
