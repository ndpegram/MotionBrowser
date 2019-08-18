<?php
/*
 * index.php
 * 
 * Main file. Mostly loads other files.
 */

// Load settings from ini file.
global $ini ;
$ini = parse_ini_file('config.ini', true, INI_SCANNER_TYPED) ;

// Get the path where index.php resides so can run under a sub director or as a named host.
$ini['root_dir'] = dirname(__FILE__) ;

// Set language.
require_once $ini['root_dir'] . "lang.inc" ; 

// Setup error handler.
require_once $ini['root_dir'] . '/util/errors.php';
$errors = new errors() ;

// HTML <head>
 require_once $ini['root_dir'] . '/head.php';

// HTML body content
?>
<body>
        <?php
            require_once $ini['root_dir'] . '/header.php';
            require_once $ini['root_dir'] . '/sidebar.php';
            require_once $ini['root_dir'] . '/main.php';
            require_once $ini['root_dir'] . '/footer.php';
        ?>
</body>
