<?php

/* 
 * Load the basic global settings.
 */

// Load settings from ini file.
global $ini ;
$ini = parse_ini_file('config.ini', true, INI_SCANNER_TYPED) ;

// Get the path where index.php resides so can run under a sub director or as a named host.
// Get both file path for PHP loads and server URL relative paths for content loads.
$ini['root_dir'] = dirname(__FILE__) ;
$ini['server_dir'] = dirname(filter_input(INPUT_SERVER, 'PHP_SELF')) ;


// Set language for gettext
require_once $ini['root_dir'] . "/lang.inc" ; 

// Setup error handler.
require_once $ini['root_dir'] . '/classes/errors.php';
$errors = new errors() ;

// Setup database access
global $db ;
require_once $ini['root_dir'] . '/libs/mysql/mysql.php' ;
$db = new database($ini['mysql']['db'], $ini['mysql']['host'], $ini['mysql']['user'], $ini['mysql']['password']) ;
$ini['db'] = $db ;
