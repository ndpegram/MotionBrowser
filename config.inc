<?php
/*
	MotionBrowser 1.0.0
	11/03/2006

	config.inc

	User interface for the result of Motion application
	Developed by Carlos Ladeira (caladeira@gmail.com)
	This software is distributed under the GNU public license

	Tested with Motion 3.2.5.1
	For more details, please visit:
	http://www.lavrsen.dk/twiki/bin/view/Motion/WebHome

	*********************************************************

	General constants e configuration values.

*/
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
	$webcam_port = array(1081, 1082, 1083, 1084);

	// setup port
	$setup_port = 8080;

	// default size for the thumbnails
	$thumb_width = 88;
	$thumb_height = 72;

	// info pour l'affichage de l'espace disque disponible
	$hauteurquota=800;
	$largeurquota=20;
	//$datadisque="/";

?>
