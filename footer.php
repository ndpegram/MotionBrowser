<?php

/* 
 * Code for display of footer elements.
 * 
 */
require_once $_SESSION['root_dir'].'/classes/diskSpaceDisplay.php' ;

?>

<div class="footer">
    <?php    diskSpaceDisplay::display() ; ?>
</div>
