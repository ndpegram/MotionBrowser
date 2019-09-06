<?php

/**
 * Extension of CalendarMonthSmall. Implements functionality used in MotionBrowser.
 *
 * CSS classes:
 *  minicalendar
 * 
 *  normal-day
 *  weekend-day
 *  selected-day

 *  calendar-month
 *  calendar-header
 *  calendar-footer 
 * 
 *  @author nigel
 */
// TODO: implement date click to display content.
// TODO: implement display content on date change.

require_once $_SESSION['root_dir'] . '/classes/CalendarMonthSmall.php';

class CalendarMonthMotion extends CalendarMonthSmall {

    protected function getCalendarCell(int $date): string {
        // TODO: set the ID of the cell to the unix timestamp of the date. 
        // The date() function format is Ymd
        // May need to set this in parent class or juse javascript from here.
        $style = "normal-day";
        $content = "";
        $ID = "";
        $script = '';

        if ($this->isWeekend($date)) {
            $style = "weekend-day";
        }

        if ($date > 0) {
            $content = $date;

            $theLast = strtotime("last day of last month", $this->getDate());
            $thisDay = strtotime("+$date days", $theLast);
            $ID = sprintf('ID=%u', $thisDay);


            if ($this->isToday($content)) {
                $style = "selected-day";
            } else {
                $script = sprintf('onClick="showDate (\'%u\') ;"', $thisDay);
            }
        }

        return "<td class='$style' $ID $script>" . $content . "</td>";
    }

    protected function getCalendarFooter(): string {
        $now = time();

        $footer = '<tr><td colspan=7 align=center class=calendar-footer>';
        // Today button
        $footer .= "<input type=button value=\"" . gettext("today") . "\" onclick='displayMonth($now) ;' >";
        $footer .= '</td></tr>';
        $footer .= parent::getCalendarFooter();
        return ($footer);
    }

    protected function getCalendarHeader(): string {
        $base = $_SESSION['server_dir'];
        $header = "<table class='minicalendar'>\n";

        $nextMonth = strtotime("next month", $this->getDate());
        $previousMonth = strtotime("last month", $this->getDate());

        $header .= "<caption class='calendar-month'>";
        $header .= "<div class=calendar-month-name>" . $this->getMonthName() . " " . $this->getYear() . "</div>";
        $header .= "<div style='float:left' onclick='displayMonth($previousMonth) ;'><img src='$base/images/arrowLeft.gif'></div>";
        $header .= "<div style='float:right;' onclick='displayMonth($nextMonth) ;'><img src='$base/images/arrowRight.gif'></div>\n";
        $header .= "</caption>\n";

        $header .= "<tr class='calendar-header'><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>\n";
        return($header);
    }

}
