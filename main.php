<?php
/*
 * Code for display of main viewer window
 * 
 */
require_once $_SESSION['root_dir'] . '/classes/eventDay.php';
require_once $_SESSION['root_dir'] . '/classes/eventDayFormatter.php';
$day = new eventDay();

?>
<script type="text/javascript" src="./js/main.js"></script>

<div class="main">

<?php echo (eventDayFormatUtils::formatEventDay(formatUtils::FORMAT_TEXT, $day)); ?>

</div>

