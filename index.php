<?php
/*
 * index.php
 * 
 * Main file. Mostly loads other files.
 */

require_once ('./config.inc');

require_once ('./head.php');
?>
<body>
        <?php
        require_once ('./header.php');
        require_once ('./sidebar.php');
        require_once ('./main.php');
        require_once ('./footer.php');
        ?>
</body>
