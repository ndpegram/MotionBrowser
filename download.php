<?php
/*
	MotionBrowser 1.0.0
	11/03/2006

	download.php

	User interface for the result of Motion application
	Developed by Carlos Ladeira (caladeira@gmail.com)
	This software is distributed under the GNU public license

	Tested with Motion 3.2.5.1
	For more details, please visit:
	http://www.lavrsen.dk/twiki/bin/view/Motion/WebHome

	*********************************************************

	Send to download a file that isn't under the root of the web site.

*/

	//
	// instead of using readfile, use this to get more performance
	// and avoiding problems with big files
	//
	function readfile_chunked($filename,$retbytes=true) 
	{
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) return false;

		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();
			if ($retbytes) $cnt += strlen($buffer);
		}

		$status = fclose($handle);

		// return num. bytes delivered like readfile() does.
		if ($retbytes && $status) return $cnt;

		return $status;
	} 
	
	$filename = $_GET['file'];
	
	// allow only to download avi files
	if (strtolower(substr(strrchr($filename,"."),1)) != "avi") return;
	
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Type: video/x-ms-wmv"); //application/force-download");
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	
	readfile_chunked("$filename");
	
	exit();
?>
