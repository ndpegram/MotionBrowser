<?php

/* 
 * Code for display of footer elements.
 * 
 */
require_once $ini['root_dir'].'/classes/diskSpaceDisplay.php' ;

?>

<div class="footer">
    <?php    diskSpaceDisplay::display() ; ?>
</div>
