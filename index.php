<?php
/*
 * index.php
 * 
 * Main file. Mostly loads other files.
 */

// Global variable for the path where index.php resides so can run under a sub director or as a named host.
global $root_dir ;
$path_parts = pathinfo($_SERVER['PHP_SELF'] );
$root_dir = $_SERVER['DOCUMENT_ROOT'] . $path_parts['dirname'] ;

// Setup error handler.
require_once $root_dir . '/util/errors.php';
$errors = new errors() ;

// Starting items
require_once $root_dir . '/config.inc';
require_once $root_dir . '/head.php';

// Body content
?>
<body>
        <?php
        require_once $root_dir . '/header.php';
        require_once $root_dir . '/sidebar.php';
        require_once $root_dir . '/main.php';
        require_once $root_dir . '/footer.php';
        ?>
</body>
