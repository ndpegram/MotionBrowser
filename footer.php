<?php

/* 
 * Code for display of footer elements.
 * 
 */
require_once $ini['root_dir'].'/classes/dataPath.php' ;
$data = new dataPath() ;
// TODO: adjust graph so that colour gradient shows green, yellow, orange, red depending on width.
?>

<div class="footer">
    <div>Disk free: <?php echo $data->getFreePercentString() ; ?></div>
    <div style='width:100%; float:left; background-color: white; border: 1px solid black ;'>
    <div class='diskGraph' style='width:<?php echo $data->getFreePercentString() ?>'>
        &nbsp;
    </div>
    </div>
</div>
