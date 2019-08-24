<?php



/**
 * Simple class to output display of disk free space.
 * @author nigel
 */
require_once $ini['root_dir'].'/classes/dataPath.php';

class diskSpaceDisplay {
    public static function display() {
    $data = new dataPath() ;
    
    ?>
    <div>Disk free space = <?php echo $data->getFreePercentString() ; ?></div>
    <meter 
        min="0"
        max="100"
        low="<?php echo $ini['disk']['freeSpaceBuffer'] ?>"
        optimum="<?php echo $ini['disk']['freeSpaceOptimum'] ?>"
        value="<?php echo $data->getFreePercent() ?>"
        >
    </meter>           
    <?php
    }
}
