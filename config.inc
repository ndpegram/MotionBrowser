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
	$config_version="1.2.1" ;

	/**
	 * Set the below if running on a development machine.
	 * Should be FALSE if on a deployment machine
	 **/
	 $testing=true ;

	//$locale='fr_FR@euro';
	$locale='en_US.UTF-8';

	// mysql related data
	$sql_host = "localhost";
	$sql_user = "motion";
	$sql_pass = "motion";
	$sql_db = "motion";

	// info for the webcam popups
	$server_addr = $_SERVER['SERVER_NAME'];
	$webcam_x = 380;
	$webcam_y = 310;
        
	/** TODO fix problem with camera index starting at 1. 
         * 
         * The problem seems to be that the cameras in the motion settings file
         * are not necessarily sequential nor zero-based.
         * Possible solution is to recode so that camera IDs are an associative
         * array of ID and port. This then is the responsibility of the user.
         * Is it possible to extract this from the motion settings files?
         */ 
	$webcam_port = array(0, 8081, 8082, 8083, 8084);

	// setup port
	$setup_port = 9080;

	// default size for the thumbnails
	$thumb_width = 88;
	$thumb_height = 72;

	// info pour l'affichage de l'espace disque disponible (Info to display the available disk space)--are these used?
	$hauteurquota=800;
	$largeurquota=20;
        
        /** 
         * Minimum free space to reserve on disk, expressed as a percentage of total disk space.
         * If the free space falls below this value, then records and files will be automatically deleted.
         */
        $freeSpaceMin = 50 ;


