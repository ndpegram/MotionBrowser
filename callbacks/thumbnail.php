<?php

/*
  Output a thumbnail of a provided bigger image.
 */
$bCreateThumbnail = true ;

// File and new size
$filename = filter_input(INPUT_GET, 'image') ;
$newwidth = filter_input(INPUT_GET, 'width') ;
$newheight = filter_input(INPUT_GET, 'height') ;

// load image
if (!file_exists($filename)) {
    $bCreateThumbnail = false ;
}

if ($bCreateThumbnail) {
    $source = imagecreatefromjpeg($filename) ;
    if ($source === false) {
        $bCreateThumbnail = false ;
    }
}

if ($bCreateThumbnail) {
    // Content type
    header('Content-type: image/jpeg');

    // Get image size
    list($width, $height) = getimagesize($filename);

    // Prepare thumbnail 
    $thumb = imagecreatetruecolor($newwidth, $newheight);

    // Resize image to the thumbnail
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    // Output result
    imagejpeg($thumb);

    // Remove temps
    imagedestroy($thumb);
    imagedestroy($source);
} else {
    if (!isset($_SESSION)) {
        session_start();
    }
    // Load and display error image
    $filename = $_SESSION['root_dir'] . "/images/npa.png";
    // Content type
    header('Content-type: image/png');
    header('Expires: 0');
    header('Content-Length: ' . filesize($filename));
    readfile("$filename");
}