<?php

/**
 * Extension of CalendarMonthSmall. Implements functionality used in MotionBrowser.
 *
* CSS classes:
 *  minicalendar
 *  normal-day
 *  weekend-day
 *  selected-day
 *  today
 *  calendar-month
 *  calendar-header
 *  calendar-footer * @author nigel
 */

require_once $ini['root_dir'].'/classes/CalendarMonthSmall.php';


class CalendarMonthMotion extends CalendarMonthSmall {
    protected function getCalendarCellContent(string $date): string {
        return parent::getCalendarCellContent($date);
    }

    protected function getCalendarFooter(): string {
        return parent::getCalendarFooter();
    }

    protected function getCalendarHeader(): string {
        global $ini ;
        $base = $ini['server_dir'] ;
        $header = "<table class='minicalendar'>\n";
        
        $header .= "\t<caption class='calendar-month'>" ;
        $header .= "<div><img src='$base/images/arrowLeft.gif'></div>" ;
        $header .= $this->getMonthName() . " " . $this->getYear() ;
        $header .= "<div><img src='$base/images/arrowRight.gif'></div>" ;
        $header .= "</caption>";
        
        $header .= "\t<tr class='calendar-header'><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>";
        return($header);
    }

}
