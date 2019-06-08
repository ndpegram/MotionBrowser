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
require_once("config.inc");
require_once("$lang");
setlocale(LC_ALL, $locales);
require_once("calendar.inc");
require_once("stream.php") ;

    // Taille d'un fichier (File size)
    function afftaille($fic)
    {
		global $megabytes, $bytes, $err_upd_size, $conn ;
		
		$tfic=filesize($fic)/1024;
		if ($tfic > 1024) {
		   $tfic=(int)($tfic / 1024). $megabytes;
	   }
		else {
		   $tfic=(int)($tfic). $bytes;
	   }
	   $query = "update security set file_size='$tfic' where filename ='$fic';" ;
		$majsize = mysqli_query($conn, $query) or die($err_upd_size."file: ".$fic." size: ".$tfic );
		//mysql_free_result($majsize);
		return $tfic;
    }
    
    // connect to database
    $conn = mysqli_connect($sql_host, $sql_user, $sql_pass) or die($err_sel_data);
    mysqli_select_db($conn, $sql_db) or die($err_sel_size);

    // Récupération du chemin des fichiers de données (Get path of the data files)
    if (!isset($datadisque))
	{
		$query = "SELECT filename FROM security order by time_stamp desc limit 0,1";
		$result = mysqli_query($conn, $query) or die ($err_req_quota);
		$mquota = mysqli_fetch_row($result);
		// Recherche du path des fichiers de données (Search for Data Files Path)
		$path_parts = pathinfo($mquota[0]);
		$datadisque=$path_parts['dirname'].'/' ;
		mysqli_free_result($result);
	}    

    //
    // check if the delete form has been submited
    //
    if (isset($_GET['what'])) {
       if ($_GET['what'] == "delete") {

	  // get the list of filenames we are going to delete
	  $query = "SELECT filename FROM security WHERE event_time_stamp IN (".$_GET['todelete'].")";
	  $result = mysqli_query($conn, $query) or die ($err_sel_file);

	  // loop for each one
	  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	      $row = mysqli_fetch_array($result);
	      $filename = $row['filename'];

	      // delete the file first
	      if (!unlink($filename)) {
	         die($err_del_file.$filename); 
	      }

	      // if no problem, delete the record from the database
	      $query = "DELETE FROM security WHERE filename='".$filename."'";
	      mysqli_query($conn, $query) or die ($err_del_req);
	  }

	  mysqli_free_result($result);
       }
    }
?>


<html>
<head>
<title><?php echo "$config_title $config_version"; ?></title>
<link rel="stylesheet" href="motionbrowser.css" type="text/css" media="all">
<script>
<!--

//
// Validate the delete form submit
//
function validateForm(frm) 
{
	var event_count = frm.event_count.value;	// load number of events
	var todelete = '';				// string with the events to delete

	// loop trough all checkbox and check if they are checked
	for (i=1; i<=event_count; i++) {
		// if they are checked, add the event to the list to be deleted
		if (eval("frm.img"+i+".checked") == true) {
			if (todelete) todelete +=',';
			todelete += eval("frm.img"+i+".value");
		}
	}

	// if there are one or more events to delete ...
	if (todelete) {
		if (confirm("<?php echo $java_confirm_delete;?>")) {
			frm.todelete.value = todelete;
			return true;
		}
		else return false;
	}
	else {
		alert("<?php echo "$java_select_delete";?>");
		return false;
	}
}

function select_all() 
{
	var event_count = document.iform.event_count.value;	// load number of events
	
	// loop trough all checkbox and check them
	for (i=1; i<=event_count; i++) {
		// if they are checked, add the event to the list to be deleted
		eval("document.iform.img"+i+".checked = true;");
	}
}

function select_none() 
{
	var event_count = document.iform.event_count.value;	// load number of events
	
	// loop trough all checkbox and uncheck them
	for (i=1; i<=event_count; i++) {
		// if they are checked, add the event to the list to be deleted
		eval("document.iform.img"+i+".checked = false;");
	}
}

function openwindow(url, title, xx, yy)
{
	var wh = open(url, title, 'scrollbars=no,status=no,menubar=no,resizable=no,toolbar=yes,width=' + xx + ',height=' + yy);
	wh.location.href = url;
	if (wh.opener == null) wh.opener = self;
	wh.focus();
}

function ToggleRowVisibility(intRowIndex)
{
	/* Mozilla 1.8alpha; see bug 77019 and bug 242368; must be higher than 1.7.x
	Mozilla 1.8a2 supports accordingly dynamic collapsing of rows in both border-collapse models
	but not 1.7.x versions */
	if(navigator.product == "Gecko" && navigator.productSub && navigator.productSub > "20041010" && (navigator.userAgent.indexOf("rv:1.8") != -1 || navigator.userAgent.indexOf("rv:1.9") != -1)) {
		document.getElementById("idtable").rows[intRowIndex].style.visibility = 
			(document.getElementById("idtable").rows[intRowIndex].style.visibility == "visible") ? "collapse" : "visible";
	}
	else {
		if(document.all && document.compatMode && document.compatMode == "CSS1Compat" && !window.opera) {
			document.getElementById("idtable").rows[intRowIndex].style.display = 
				(document.getElementById("idtable").rows[intRowIndex].style.display == "block") ? "none" : "block";
		}
		// Mozilla prior to 1.8a2, Opera 7.x and MSIE 5+
		else if(document.getElementById && document.getElementById("idtable").rows) {
			document.getElementById("idtable").rows[intRowIndex].style.display = 
				(document.getElementById("idtable").rows[intRowIndex].style.display == "") ? "none" : "";
		}
	}
}

function plusclick(image)
{
	if (image.src.indexOf('mais.gif') != -1) image.src = "./menos.gif";
	else image.src = "./mais.gif";
}

//-->
</script>
</head>

<?php
	// if no day has been selected in the calendar, use current time
	if (isset($_GET['view_date'])) $view_date = $_GET['view_date'];
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
	$result = mysqli_query($conn, $query) or die ($err_sel_que1.$query);
	$numofrows = mysqli_num_rows($result);
	
	// get also a list of all cameras that has events for the selected day
	// fill the $camera_list with the result
	$query = 
		'SELECT distinct camera '.
		'FROM security '.
		'WHERE event_time_stamp >= '.$date.'000000 '.
		'AND event_time_stamp <= '.$date.'235959 '.
		'ORDER BY camera';
	$camera_list = mysqli_query($conn, $query) or die ($err_sel_que2.$query);
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
	// count the number of events (images) on screen
	$event_count = 0;
	$row_count = 2;

	// prepare the form that will allow us to delete events
	echo "<form name=iform action=".$_SERVER['PHP_SELF']." method=get onSubmit=\"javascript:return validateForm(this);\">";
	echo "<input type=hidden name=view_date value=".$view_date.">";
	echo "<input type=hidden name=what value=delete>\n";
	echo "<input type=hidden name=todelete value=>\n";
	echo '<table border=0> <tr><td valign=top>';

	echo "<span class=title><b>$config_title $config_version</b></class>\n";
	echo "<br><br>\n";

	// draw calendar
	echo calendar($view_date);

	echo "<center><br>\n";
	echo "<a href=\"javascript:select_all();\">".$all."</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:select_none();\">".$nothing."</a><br>\n";
	echo "<input type=submit value=\"$erase_selection\">\n";

	echo "<br><br>\n";
	echo "<p><a href=\"http://$server_addr:$setup_port\" target=_blank>$config_motion</a></p>\n";
	echo "<p>&nbsp;</p>\n";
	echo "<span class=credits>$config_credits<br>";
	echo "<a href=\"$config_mailto\">$config_mailname</a></class>\n";
	echo "<br><br><br>\n";

	echo '</td><td>&nbsp;&nbsp;</td><td valign=top>';
	
	// if there are any events on the present day
	if ($numofrows != 0) {

		echo "<TABLE id=idtable border=1 cellspacing=0 cellpadding=4 class=timeline>\n";

		// show the column header only if they have any event
		echo "<TR class=timeline-header><th>&nbsp;</th>";
//		echo "<pre>".print_r($cameras)."</pre>" ;
		foreach ($cameras as $cam) {
			$webcam = "http://$server_addr:".$webcam_port[($cam)]."/";
			$title = "$camera_name $cam";
			echo "<Th>&nbsp;$title<a href=\"javascript:openwindow('$webcam', '$title', $webcam_x, $webcam_y);\">&nbsp;&nbsp;<img src=icon_video.gif border=0 alt=\"$see_camera\"></a>&nbsp;</Th>";
		}
		echo "</tr>\n";
		
		$image = '';
		$timestamp = 0;
		$hour = -1;
		$camera_index = 0;
		$temp_td = '';
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
					echo "<script>\n<!--\ndocument.getElementById('countevents".$hour."').innerHTML = '".$hour_event_count."';\n//-->\n</script>\n";
					$hour_event_count = 0;
				}

				// start a new row
				echo '<tr><td valign=top class=timeline-hour>'.substr($hourev+100,1).$hours.'</td>';
				echo "<td colspan=".$num_cameras." class=timeline-row-header>";
				echo "&nbsp;<a href=\"javascript:ToggleRowVisibility(".$row_count.");\">";
				echo "<img src=mais.gif border=0 onclick=\"plusclick(this);\">";
				echo "</a>&nbsp;&nbsp;";
				echo "<span id=countevents".$hourev."></span> $events";
				echo "</td></tr>";
				echo '<tr><td class=timeline-hour>&nbsp;</td>';
				$row_count += 2;

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

			// new event
			$event_count++;

			// if not the first event on this camera and hour, put a separation
			if ($temp_td) $temp_td .= "<hr size=1 width=95% color=gray>";

			// add to the $temp_td the html for this event
			$temp_td .= 
				"<a href=\"stream.php?file=".$row['filename']."\">".
//				"<a href=\"download.php?file=".$row['filename']."\">".
				"<img src=\"thumbnail.php?image=$image&width=$thumb_width&height=$thumb_height\" border=0>".
				"</a><br>".
				"<input type=checkbox name=img$event_count value=$ev_ts>".
				"&nbsp;&nbsp;&nbsp;".$row['timefield']." (".(empty($row['file_size']) ? afftaille($row['filename']) : $row['file_size']) .")<br>";

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
		echo "<script>\n<!--\ndocument.getElementById('countevents".$hour."').innerHTML = '".$hour_event_count."';\n//-->\n</script>\n";
		
		// now let's close the table and be done with it
		echo "</TABLE>\n";
	}
	else echo '<p><br>'.$no_events.'</p>';
	
	// close the main table
	echo '</td></tr></table>';

	// free $result variable	
	mysqli_free_result($result);
	
	// set the number of event on this page and close the form
	echo "<input type=hidden name=event_count value=".$event_count.">\n";
	echo "</form>\n";

	// Affichage du quota disque
	if (isset($datadisque))
	{
		$ratio= 1 - (disk_free_space($datadisque)/disk_total_space($datadisque) ) ;
		echo "<div class=\"quota\">\n";
		echo $vol1_quota.$datadisque.$vol2_quota.number_format($ratio*100,2,","," ")." %\n";
		echo "<img src=\"affquota.php?ratio=".$ratio."\">\n";
		echo "</div>\n";
	}
?>

<script>
<!--

// collapse the timeline table
for (i = 2; i < <?php echo $row_count; ?>; i += 2){
	ToggleRowVisibility(i);
} 

<?php
if ($ratio > .9)
    echo 'alert("'.$space1.$datadisque.$low_space.' ")';
?>
//-->
</script>

</body>
</html>
