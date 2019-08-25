<?php
/*
 * index.php
 * 
 * Main file. Mostly loads other files.
 */

require_once './setup.php';

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
