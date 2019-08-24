<?php

/* 
 * Callback to get a month calendar. A unix timestamp is passed as ts.
 */

// As this is called outside of the normal launch of index.php, we need to set some ini vars ourselves.
global $ini ;
$ini['root_dir'] = realpath(__DIR__ . "/../") ; //root dir is one up from this file's dir.
$ini['server_dir'] = dirname(filter_input(INPUT_SERVER, 'PHP_SELF')) . '/..' ;
        
require_once $ini['root_dir'] . '/classes/CalendarMonthMotion.php';

$ts = filter_input(INPUT_POST, "ts") ;
$cal = new CalendarMonthMotion($ts) ;
echo ($cal->getHTML()) ;