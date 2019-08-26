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
	putenv("LC_ALL=".$_SESSION['locale']);
	setlocale(LC_ALL, $_SESSION['locale']);
	bindtextdomain('catalog', $_SESSION['root_dir'] . '/gettext/i18n');
	textdomain('catalog');
}
