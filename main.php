<?php
/*
 * Code for display of main viewer window
 * 
 */

?>
<script type="text/javascript" src="./js/main.js"></script>

<div class="main">
    
<?php 
//TODO: add code to check if over disk quota and delete days until not. See deleteOldestDay in old index.php:180. 
// Perhaps use callback to run once document loaded and then refresh disk usage display.
// 
// TODO: TEST THIS!
// Clean disk before displaying today's info.
new diskCleaner() ;

require_once $_SESSION['root_dir'] . '/callbacks/getEvents.php';
?>

</div>

