<?php

/**
 * js_gettext.php
 *
 * Callback used to get localised strings from within javascript.
 * This is used to provide translated values for alerts, etc.
 */

require_once ("lang.inc") ;


if (!filter_has_var(INPUT_POST, 'message')) {
	die (gettext("js gettext no message")) ;
}

$message = filter_input(INPUT_POST, 'message'); 
echo gettext($message) ;
return ;

/**
 * Cannot get here, so we include gettext calls below which are only
 * used in javascript. This way gettext will create entries for
 * strings which are only used in javascript.
 */
$message1 = gettext("No items selected for deletion") ;
$message2 = gettext("Delete selected items") ;
$message3 = gettext("Error deleting") ;
