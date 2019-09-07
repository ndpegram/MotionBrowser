<?php

/**
 * js_gettext.php
 *
 * Callback used to get localised strings from within javascript.
 * This is used to provide translated values for alerts, etc.
 */

require_once ("lang.inc") ;


if (!isset($_POST['message'])){
	die (gettext("js gettext no message")) ;
}

$message = $_POST['message'] ;
echo gettext($message) ;
return ;

/**
 * Cannot get here, so we include gettext calls below which are only
 * used in javascript. This way gettext will create entries for
 * strings which are only used in javascript.
 */
$message = gettext("No items selected for deletion") ;
$message = gettext("Delete selected items") ;
$message = gettext("Error deleting") ;
