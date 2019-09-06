<?php

/*
 * Return the list of events for the date specified in the UNIX timestamp passed
 * as a POST var in 'ts'. If nothing in 'ts', then use the current date.
 */
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION['root_dir'] . '/classes/eventDay.php';
require_once $_SESSION['root_dir'] . '/classes/eventDayFormatter.php';

if (filter_has_var(INPUT_POST, 'ts')) {
    $ts = filter_input(INPUT_POST, 'ts');
    $day = new eventDay($ts);
} else {
    $day = new eventDay();
}

echo (eventDayFormatUtils::formatEventDay(formatUtils::FORMAT_HTML, $day));
