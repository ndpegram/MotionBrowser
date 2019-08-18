<?php
/**
 *
 * Set the default language.
 *
 **/

if (!function_exists("gettext")){
    die ("gettext is not installed\n") ;
}
else{
	putenv("LC_ALL=".$ini['locale']);
	setlocale(LC_ALL, $ini['locale']);
	bindtextdomain('catalog', $ini['root_dir'] . '/gettext/i18n');
	textdomain('catalog');
}
