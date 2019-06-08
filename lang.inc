<?php
/**
 *
 * Set the default language.
 *
 **/

require_once ("config.inc") ;

if (!function_exists("gettext")){
    die ("gettext is not installed\n") ;
}
else{
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain('catalog', dirname(__FILE__).'/gettext/i18n');
	textdomain('catalog');
}

