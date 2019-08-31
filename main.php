<?php
/*
 * Code for display of main viewer window
 * 
 */
require_once $_SESSION['root_dir'] . '/classes/eventDay.php';
$day = new eventDay();

?>
<script type="text/javascript" src="./js/main.js"></script>

<div class="main">

<?php echo ($day->toHTML()); ?>

</div>

