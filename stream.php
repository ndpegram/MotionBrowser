<?php
include_once 'VideoStream.php';

// Array of downloadable video file extensions
$videos = array ('avi', 'mp4', 'mpeg') ;
	
// allow only to download video files
$filename = $_GET['file'];
$ext = strtolower(substr(strrchr($filename,"."),1)) ;
if (!in_array($ext, $videos)) return ;
	
$stream = new VideoStream($filename);
$stream->start();exit();
