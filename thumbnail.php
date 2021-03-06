<?php
/*
	MotionBrowser 1.1
	20190422

	config.inc

	User interface for the result of Motion application
	Developed by Carlos Ladeira (caladeira@gmail.com)
	Updated by Nigel D. Pegram (ndpegram@gmail.com)
	This software is distributed under the GNU public license

	Tested with Motion 4.0
	For more details, please visit:
	http://www.lavrsen.dk/twiki/bin/view/Motion/WebHome

	*********************************************************

	Output a thumbnail of a provided bigger image.

*/

	// Content type
	header('Content-type: image/jpeg');
	
	// File and new size
	$filename = $_GET['image'];
	$newwidth = $_GET['width'];
	$newheight = $_GET['height'];
	
	// Get image size
	list($width, $height) = getimagesize($filename);
	
	// Prepare thumbnail and load image
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$source = imagecreatefromjpeg($filename);
	
	// Resize image to the thumbnail
	imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
	// Output result
	imagejpeg($thumb);
	
	// Remove temps
	imagedestroy($thumb);
	imagedestroy($source);
?> 

