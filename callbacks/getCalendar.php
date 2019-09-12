<?php

/* 
 * Callback to get a month calendar. A unix timestamp is passed as ts.
 */
session_start() ;

require_once $_SESSION['root_dir'] . '/lang.inc' ;
require_once $_SESSION['root_dir'] . '/classes/CalendarMonthMotion.php';

$ts = filter_input(INPUT_POST, "ts") ;
$cal = new CalendarMonthMotion($ts) ;
echo ($cal->getHTML()) ;