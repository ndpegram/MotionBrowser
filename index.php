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

	This web page reads the mysql database filled by Motion and
	output events by day
	It creates small thumbnail were you can click to watch the avi
	file for the same event.
	You can also delete the events you select.

	It's a good interface in case you are running Motion on a computer
	without monitor/keyboad/mouse, only network!

	It is supposed to work with the following motion.conf
	optios set as shown (for better results):

	pre_capture 8		(works for me with a framerate of 6)
	post_capture 8		(ie)
	output_all off
	output_normal best (or) first
	output_motion off
	text_event %Y%m%d%H%M%S
	ffmpeg_cap_new on
	ffmpeg_video_codec msmpeg4
	sql_log_image on
	sql_log_snapshot off
	sql_log_mpeg on
	sql_log_timelapse off
	sql_query INSERT ...	(I use the default)
	mysql_db motion		(my database name)
	mysql_host localhost
	mysql_user ...		(the user name i created in MySQL)
	mysql_password ...	(the password associated with user)

*/
require_once("lang.inc"); // brings in config.inc
require_once("calendar.inc");
require_once("stream.php") ;

	/**
	 * Dynamically generated forms are used to create POST vars for actions.
	 *
	 * POST VARS used
	 *
	 * Variable  : value
	 *
	 * view_date	:	date to display (e.g. on calendar click)
	 * what			:	action to take. Options = {delete}
	 * todelete		:	files to delete from database and disk
	 *
	 */

    /**
     * @param String $divID ID of element whose content will be set to the 
     *                      number of elements in that hour for that camera.
     * @param int $numEvents The number of events.
     * @return void Nothing.
     * 
     * FIXME: does not yet work.
     */
    function setEventCount($divID, $numEvents){
        $events = sprintf(ngettext("%d event", "%d events", $numEvents), $numEvents) ; 
        echo "<script>\n<!--\ndocument.getElementById('#$divID').innerHTML = '".$events."';\n//-->\n</script>\n";       
    }

    /**
     * Calculate the size of the specified file. The database is updated to 
     * match. Sizes are scaled to bytes, kilobytes or megabytes as required.
     * 
     * @global object $conn Connection to SQL database
     * @param type $szFileName Name of file whose size is calculated
     * @return String Size of file.
     * TODO: check on production site
     */
    function setFileSize($szFileName)
    {
        global $conn;

        $fSize = filesize($szFileName) ;
        switch ($fSize) {
            case ($fSize >= 1024 *1024):
                $szUnits = "MB" ;
                break;

            case ($fSize >= 1024):
                $szUnits = "KB" ;
                break;

            case ($fSize > 0):
                $szUnits = "B" ;
                break;
            
            default:
                return ("");
        }
        
        $szSize = sprintf("%d %s", $fSize, $szUnits) ;
        $query = "update security set file_size='$fSize' where filename ='$szFileName';";
        mysqli_query($conn, $query) or 
                die(printf(gettext("error updating file size %s %s"), $szFileName, $szSize)) ;

        return $szSize;
}

    /**
     * Delete files and database entries. Called in response to POST
     * @param	{}		IDs of items to delete.
     * @return	{void}	nothing
     */
     function deleteItems($IDs){
		global $testing, $conn  ;
		$filenames = '' ;
		$nFiles = 0 ;

		$conn = getDBConnection() ;

		if (is_array($IDs)){
			$IDs = implode(", ", $IDs) ;
		}

		// get the list of filenames we are going to delete
		$query = "SELECT `filename` FROM `security` WHERE `event_time_stamp` IN ($IDs)" ;
                // TODO: convert die to use one generic gettext call with sprintf and multiple string replacements.
		$result = mysqli_query($conn, $query) or
			die (gettext("select query failed") . '<br />' .
				gettext ("Debugging errno") . ': ' . mysqli_connect_errno() . '<br />' .
				gettext ("Debugging error") . ': ' . mysqli_connect_error() . '<br />' .
				__FILE__. "<br /> " .
				gettext("line").": ".__LINE__ . '<br />' .
				gettext("query: ") . $query . '<br />' .
				gettext("result: "). $result);

		// loop for each one
		for ($i = 0; $i < mysqli_num_rows($result); $i++) {
			// TODO: add sanity check to delete rows without matching files and vice versa (possibly on each call, or possibly using cron).
			$row = mysqli_fetch_array($result);
			$filename = $row['filename'];

			if (!$testing){
				// delete the file first
				if (!unlink($filename)) {
					die(sprintf(gettext("Error deleting %s"), $filename)) ;
				}

				// if no problem, delete the record from the database
				$query = "DELETE FROM security WHERE filename='".$filename."'";
				mysqli_query($conn, $query) or
					die (gettext("error deleting file") . '<br />' .
						gettext ("Debugging errno") . ': ' . mysqli_connect_errno() . '<br />' .
						gettext ("Debugging error") . ': ' . mysqli_connect_error() . '<br />' .
						__FILE__. "<br /> " .
						gettext("line").": ".__LINE__ . '<br />' .
						gettext("query: ") . $query . '<br />' .
						gettext("result: "). $result);
			}
			else {
				// Running development machine/version--take no irreversible actions
				$nFiles++ ;
				$filenames .= '"' . $filename . '"' . ', ' ;
			}
		}

		if ($testing){
			// trim trailing comma
			$filenames = rtrim ($filenames, ', ') ;

		  $query = 'select count(*) from `security` where `filename` in (' . $filenames . ')' ;
		  $result = mysqli_query ($conn, $query) or
			die (gettext("select query failed") . '<br />' .
				gettext ("Debugging errno") . ': ' . mysqli_connect_errno() . '<br />' .
				gettext ("Debugging error") . ': ' . mysqli_connect_error() . '<br />' .
				__FILE__. "<br /> " .
				gettext("line").": ".__LINE__ . '<br />' .
				gettext("query: ") . $query . '<br />' .
				gettext("result: "). $result);

		  $row = mysqli_fetch_row($result) ;
		  echo '<h3>Delete</h3><p>Query returned '. $row[0] .' items to be deleted out of ' . $nFiles . ' items selected to delete</p>' ;
		  $filenames = str_replace(',', '<br />', $filenames) ;
		  echo "<pre>$filenames</pre>" ;
		  die ;
		}

		mysqli_free_result($result);
	 }

    function getDBConnection(){
		// connect to database
		global $sql_host, $sql_user, $sql_pass, $sql_db ;

		$conn = mysqli_connect($sql_host, $sql_user, $sql_pass) or
			die(gettext("error establishing database connection") . '<br />'  .
				gettext ("Debugging errno") . ': ' . mysqli_connect_errno() . '<br />' .
				gettext ("Debugging error") . ': ' . mysqli_connect_error() . '<br />' .
				__FILE__. "<br /> " .
				gettext("line").": ".__LINE__
				);
		mysqli_select_db($conn, $sql_db) or
			die(gettext("error selecting database")) . '<br />'  .
				gettext ("Debugging errno") . ': ' . mysqli_connect_errno() . '<br />' .
				gettext ("Debugging error") . ': ' . mysqli_connect_error() . '<br />' .
				__FILE__. "<br /> " .
				gettext("line").": ".__LINE__ ;

		return ($conn) ;
	}

	if (!isset($conn)){
		$conn = getDBConnection() ;
	}

    // R�cup�ration du chemin des fichiers de donn�es (Get path of the data files)
    if (!isset($datadisque))
	{
		$query = "SELECT filename FROM security order by time_stamp desc limit 0,1";
		$result = mysqli_query($conn, $query) or die (gettext("err_req_quota"));
		$mquota = mysqli_fetch_row($result);
		// Recherche du path des fichiers de donn�es (Search for Data Files Path)
		$path_parts = pathinfo($mquota[0]);
		$datadisque=$path_parts['dirname'].'/' ;
		mysqli_free_result($result);
	}

    // Act on POST form.
    if (isset($_POST['what'])) {
		// check if the delete form has been submited
       if ($_POST['what'] == gettext("delete")) {
		   deleteItems($_POST['todelete']) ;
       }
    }
?>


<html>
<head>
	<title><?php echo gettext("config_title")." ".gettext("config_version"); ?></title>
	<link rel="stylesheet" href="motionbrowser.css" type="text/css" media="all">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="./calendar.js" ></script>
	<script src="./motionbrowser.js" ></script>

</head>

<?php
	// if no day has been selected in the calendar, use current time
	if (isset($_POST['view_date'])) $view_date = $_POST['view_date'];
	else $view_date = time();

	// get all events for the selected day in a order that allow us to
	// show the result in a nice way!
	$date = date('Ymd', $view_date);
	$query =
		'SELECT *, TIME(event_time_stamp) as timefield, HOUR(event_time_stamp) as hourfield, '.
		'event_time_stamp+0 as time_stamp, file_size '.
		'FROM security '.
		'WHERE event_time_stamp >= '.$date.'000000 '.
		'AND event_time_stamp <= '.$date.'235959 '.
		//'AND file_type=8 '. // list only movies
		'ORDER BY hourfield, camera, timefield, file_type';
	$result = mysqli_query($conn, $query) or die (gettext("err_sel_events").$query); // was err_sel_que1
	$numofrows = mysqli_num_rows($result);

	// get also a list of all cameras that has events for the selected day
	// fill the $camera_list with the result
	$query =
		'SELECT distinct camera '.
		'FROM security '.
		'WHERE event_time_stamp >= '.$date.'000000 '.
		'AND event_time_stamp <= '.$date.'235959 '.
		'ORDER BY camera';
	$camera_list = mysqli_query($conn, $query) or die (gettext("err_sel_cameras").$query); // was err_sel_que2
	$cameras = array();
	$num_cameras = 0;
	for (; $num_cameras < mysqli_num_rows($camera_list); $num_cameras++) {
		$row = mysqli_fetch_array($camera_list);
		$cameras[$num_cameras] = $row['camera'];
	}
	mysqli_free_result($camera_list);
?>

<body>

<?php
	echo '<table border=0> <tr><td valign=top>';

	echo "<span class=title><b>".gettext("config_title")." * ".gettext("config_version")."</b></span>\n";
	echo "<br><br>\n";

	// draw calendar
	echo calendar($view_date);

	echo "<center><br>\n";
	echo '<p><input type=button onclick="javascript:select_all();" value="'.gettext("all").'"></p>';
	echo '<p><input type=button onclick="javascript:select_none();" value="'.gettext("nothing").'"></p>'."\n" ;
	echo '<p><input type=button onclick="javascript:deleteSelection();" value="'.gettext("erase_selection").'"></p>'."\n";

	echo "<p>&nbsp;</p>\n";
	echo "<p><a href=\"http://$server_addr:$setup_port\" target=_blank>".gettext("config_motion")."</a></p>\n";
	echo "<p>&nbsp;</p>\n";
	echo "<span class=credits>".gettext("config_credits")."<br>";
	echo "<a href=\"mailto:".gettext("config_mailname")."\">".gettext("config_mailname")."</a></class>\n";
	echo "<br><br><br>\n";

	echo '</td>';
	echo '<td style="width:2em;"></td>';
	echo '<td valign=top>';

	// if there are any events on the present day
	if ($numofrows != 0) {

		echo "<TABLE id=idtable border=1 cellspacing=0 cellpadding=4 class=timeline>\n";

		// show the column header only if they have any event
		echo "<TR class=timeline-header><th>&nbsp;</th>";
//		echo "<pre>".print_r($cameras)."</pre>" ;
		foreach ($cameras as $cam) {
			$webcam = "http://$server_addr:".$webcam_port[($cam)]."/";
			$title = sprintf(gettext("camera name %d"), $cam) ;
			echo "<Th>&nbsp;$title<a href=\"javascript:openwindow('$webcam', '$title', $webcam_x, $webcam_y);\">&nbsp;&nbsp;<img src=icon_video.gif border=0 alt=\"".gettext("see_camera")."\"></a>&nbsp;</Th>";
		}
		echo "</tr>\n";

		$image = '';
		$timestamp = 0;
		$hour = -1;
		$camera_index = 0;
		$temp_td = '';
                // TODO: could this be replaced with information from mySQL? (redesign query)
		$hour_event_count = 0;

		//
		// The $result recordset is expected with two record for each event
		// the first with the jpeg image (image_type = 1) and
		// the second with the avi file (image_type = 8), both with the same
		// event timestamp.
		//
		for($i = 0; $i < $numofrows; $i++) {

			$row = mysqli_fetch_array($result); //get a row from our result set
			$ev_ts = $row['time_stamp'];


			switch ($row['file_type']) {
				case 1:	// jpeg file
					// if is another image from the same event, ignore it
					if ($ev_ts == $timestamp) continue 2;

					$timestamp = $ev_ts;
					$image = $row['filename'];

					// jump to next loop to get correspondent movie file
					continue 2;

				case 8:	// movie file
					// if isn't the same event, next row
					if ($ev_ts != $timestamp) continue 2;
					break;

				default:
					// if other type, ignore and jump to next row
					continue 2;
			}

			// get hour and camera for this event
			$hourev = $row['hourfield'];
			$cameraev = $row['camera'];

			// has the hour changed?
			if ($hour != $hourev) {
				// if not first row (hour)
				if ($hour != -1) {
					// if $temp_td not empty, flush it
					if ($temp_td != '') {
						echo '<td valign=top>'.$temp_td.'</td>';
						$temp_td = '';
						$camera_index++;
					}

					// while not the last column of this row ...
					while ($camera_index < count($cameras)) {
						echo "<td>&nbsp;</td>";
						$camera_index++;
					}

					// end this row
					echo "</tr>\n";
                                        // Go back and set the hourly event count.
                                        //setEventCount('countevents'.$hour) ;
                                        $events = sprintf(ngettext("%d event", "%d events", $hour_event_count), $hour_event_count) ; 
					echo "<script>\n<!--\ndocument.getElementById('countevents".$hour."').innerHTML = '".$events."';\n//-->\n</script>\n";
					$hour_event_count = 0;
				}

				// Start a new set of hourly events
                                // Summary header row
				echo '<tr class="hour-summary" id="'.$hourev.'">';
                                echo '<td valign=top class=timeline-hour>'.substr($hourev+100,1).gettext("hours").'</td>';
				echo "<td colspan=".$num_cameras." class=timeline-row-header>";
				echo '<img class="plus" id="'.$hourev.'" src=mais.gif border=0 onclick="plusclick(this);">';
				echo '<span id=countevents'.$hourev.'></span>' ;
				echo '</td>' ;
                                echo '</tr>';
                                // Details--images and videos
				echo '<tr class="hour-events" id="'.$hourev.'">' ;
                                echo '<td class=timeline-hour>&nbsp;</td>';

				$camera_index = 0;
				$hour = $hourev;
			}

			// has camera changed?
			if ($cameraev != $cameras[$camera_index]) {
				// if $temp_td not empty, flush it
				if ($temp_td != '') {
					echo '<td valign=top>'.$temp_td.'</td>';
					$temp_td = '';
					$camera_index++;
				}

				// go to the correct camera column
				while ($camera_index < count($cameras) AND $cameraev != $cameras[$camera_index]) {
					echo '<td>&nbsp;</td>';
					$camera_index++;
				}
			}

			// if not the first event on this camera and hour, put a separation
			if ($temp_td) $temp_td .= "<hr size=1 width=95% color=gray>";

			// add to the $temp_td the html for this event
			$temp_td .=
				"<a href=\"stream.php?file=".$row['filename']."\">".
//				"<a href=\"download.php?file=".$row['filename']."\">".
				"<img src=\"thumbnail.php?image=$image&width=$thumb_width&height=$thumb_height\" border=0>".
				"</a><br>".
				"<input type=checkbox name=$ev_ts value=$ev_ts>".
				"&nbsp;&nbsp;&nbsp;".$row['timefield']." (".(empty($row['file_size']) ? setFileSize($row['filename']) : $row['file_size']) .")<br>";

			$hour_event_count++;

			// reset timestamp value because this event is complete
			$timestamp = 0;

		}

		// if $temp_td not empty, flush it
		if ($temp_td != '') {
			echo '<td valign=top>'.$temp_td.'</td>';
			$temp_td = '';
			$camera_index++;
		}

		// while not the last column of this row ...
		while ($camera_index < count($cameras)) {
			echo "<td>&nbsp;</td>";
			$camera_index++;
		}

		// end this row
		echo "</tr>\n";
                $events = sprintf(ngettext("%d event", "%d events", $hour_event_count), $hour_event_count) ; 
                echo "<script>\n<!--\ndocument.getElementById('countevents".$hour."').innerHTML = '".$events."';\n//-->\n</script>\n";

		// now let's close the table and be done with it
		echo "</TABLE>\n";
	}
	else {
            echo '<p><br>'.gettext("no_events").'</p>';
        }
        
	// close the main table
	echo '</td></tr></table>';

	// free $result variable
	mysqli_free_result($result);


	// Disk quota display
	if (isset($datadisque))
	{
		$ratio = 1 - (disk_free_space($datadisque)/disk_total_space($datadisque) ) ;
		echo "<div class=\"quota\">\n";
		printf (gettext("disk space used %s %f"), $datadisque, number_format($ratio*100,2)) ;
		echo "<img src=\"affquota.php?ratio=".$ratio."\">\n";
		echo "</div>\n";
	}
?>

<script>
<!--
    // TODO: can this be moved to the document ready javascript function? Perhaps use jquery to get percentage from DOM and work on that.
<?php
    if ($ratio > .9){
        echo 'alert("'.gettext("disk_space").' '.$datadisque.gettext("low_space").'. ")';
    }   
?>
//-->
</script>

</body>
</html>
