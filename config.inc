<?php
/*
	MotionBrowser 

	config.inc

	User interface for the result of Motion application
	Developed by Carlos Ladeira (caladeira@gmail.com)
	Updated by Nigel D. Pegram (ndpegram@gmail.com)
	This software is distributed under the GNU public license

	Tested with Motion 4.0
	For more details, please visit:
	http://www.lavrsen.dk/twiki/bin/view/Motion/WebHome

	*********************************************************

	General constants and configuration values.

*/
	$config_version="1.2" ;

	//$lang="fr";
	//$locales='fr_FR@euro';
	$lang="en";
	$locales='en_US.UTF-8';

	// mysql related data
	$sql_host = "localhost";
	$sql_user = "motion";
	$sql_pass = "motion";
	$sql_db = "motion";

	// info for the webcam popups
	$server_addr = $_SERVER['SERVER_ADDR'];
	$webcam_x = 380;
	$webcam_y = 310;
	$webcam_port = array(8081, 8082, 8083, 8084);

	// setup port
	$setup_port = 9080;

	// default size for the thumbnails
	$thumb_width = 88;
	$thumb_height = 72;

	// info pour l'affichage de l'espace disque disponible (Info to display the available disk space)
	$hauteurquota=800;
	$largeurquota=20;
	
?>
