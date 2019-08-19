<?php

/* 
 * Code for display of footer elements.
 * 
 */
require_once $ini['root_dir'].'/classes/dataPath.php' ;
$data = new dataPath() ;

?>

<div class="footer">
    <div>Disk free space = <?php echo $data->getFreePercentString() ; ?></div>
    <meter 
        min="0"
        max="100"
        low="<?php echo $ini['disk']['freeSpaceBuffer'] ?>"
        optimum="<?php echo $ini['disk']['freeSpaceOptimum'] ?>"
        value="<?php echo $data->getFreePercent() ?>"
        >
    </meter>           
</div>
