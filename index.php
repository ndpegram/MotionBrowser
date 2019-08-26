<?php
/*
 * index.php
 * 
 * Main file. Mostly loads other files.
 */
session_start() ;
// Get the path where index.php resides so can run under a sub director or as a named host.
// Get both file path for PHP loads and server URL relative paths for content loads.
$_SESSION['root_dir'] = dirname(__FILE__) ;
$_SESSION['server_dir'] = dirname(filter_input(INPUT_SERVER, 'PHP_SELF')) ;

require_once $_SESSION['root_dir'].'/setup.php';

// HTML <head>
 require_once $_SESSION['root_dir'] . '/head.php';

// HTML body content
?>
<body>
        <?php
            require_once $_SESSION['root_dir'] . '/header.php';
            require_once $_SESSION['root_dir'] . '/sidebar.php';
            require_once $_SESSION['root_dir'] . '/main.php';
            require_once $_SESSION['root_dir'] . '/footer.php';
        ?>
</body>
