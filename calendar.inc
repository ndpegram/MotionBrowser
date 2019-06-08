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

	Generate html output for a calendar of a provided date.

*/

function calendar($date)
{
	global $today ;
	
	$year = date('Y', $date);
	$month = date('m', $date);
	$day = date('j', $date);

	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));

	$weekday = ($weekday + 6) % 7;

	// Create first day of week based on locales...
        $day0=mktime(0,0,0,1,3,0);
	
	$listdays = '' ;
	for($ij=0;$ij<=6;$ij++)
	   {
           $listdays.="<th>".strtoupper(substr(strftime("%a",$day0),0,1))."</th>";
	   $day0+=86400;
	   }
        //istdays='<th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th>';

	$calendar = 
		'<table class="minicalendar">'."\n".
		'<caption class="calendar-month">'.
		'<a href="'.$_SERVER['PHP_SELF'].'?view_date='.mktime(0, 0, 0, $month-1, $day, $year).'"><img src=lefts1.gif border=0></a>&nbsp;&nbsp;'.
		htmlentities(ucfirst($month_name)).'&nbsp;'.$year.
		'&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?view_date='.mktime(0, 0, 0, $month+1, $day, $year).'"><img src=rights1.gif border=0></a>'.
		'</span></caption>'."\n".'<tr class="calendar-header">'.
		$listdays.
		//'<th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th>'.
		"</tr>\n<tr>";

	if ($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>';

	for ($dd=1,$days_in_month=gmdate('t',$first_of_month); $dd<=$days_in_month; $dd++,$weekday++){

		if ($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}

		if ($weekday > 4) $day_class ='weekend-day';
		else $day_class = 'normal-day';

		if ($dd == $day) $calendar .= '<td class="selected-day">'.$dd.'</td>';
		else $calendar .= 
			  '<td class="'.$day_class.'">'.
			  '<a href="'.$_SERVER['PHP_SELF'].'?view_date='.mktime(0, 0, 0, $month, $dd, $year).'">'.$dd.'</a>'.
			  '</td>';

	}

	if ($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>';

	$calendar .= '</tr>';
	$calendar .= '<tr><td colspan=7 align=center class=calendar-footer><a href="'.$_SERVER['PHP_SELF'].'?view_date='.time().'"'.">$today</a></td></tr>";
	$calendar .= "</table>\n";

	return $calendar;
}
?>
