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
*/

include("config.inc");
$ratio=strtr($_GET['ratio'],",",".");
header("Content-type: image/png");
$im = imagecreate($hauteurquota, $largeurquota);
$bfonce= imagecolorallocate($im,  83,  79, 179);
$bclair= imagecolorallocate($im, 109, 167, 203);
$blanc = imagecolorallocate($im, 255, 255, 255);

    // rectangle showing remaining space on pictures/movies volume
    if ($ratio > 0.5)
        $blanc = imagecolorallocate($im, 255, 255*((1-$ratio)*2) , 0);

imagefill($im, 0, 0, $bfonce);
imagefilledrectangle($im, 4,  3, $hauteurquota * $ratio, $largeurquota - 4 , $bclair);
imagefilledrectangle($im, $hauteurquota * $ratio , 3, $hauteurquota-4 , $largeurquota - 4, $blanc );

imagepng($im);
imagedestroy($im);
?>
