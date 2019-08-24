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

 *  *  calendar-month
 *  calendar-header
 *  calendar-footer * @author nigel
 */
require_once $ini['root_dir'] . '/classes/CalendarMonthSmall.php';

class CalendarMonthMotion extends CalendarMonthSmall {

    protected function getCalendarCell(int $date): string {
        $style = "normal-day";
        $content = "";

        if ($date > 0) {
            $content = $date;

            if ($this->isWeekend($content)) {
                $style = "weekend-day";
            }

            if ($this->isToday($content)) {
                $style = "selected-day";
            }
        }

        return "<td class='$style'>" . $content . "</td>";
    }

    protected function getCalendarFooter(): string {
        global $ini ;
        $footer = '<tr><td colspan=7 align=center class=calendar-footer>';
        // Today button
        $footer .= '<input type=button value="' . gettext("today") . '" onclick="document.location=\'' . $ini['server_dir'] . '\'; " >';
        $footer .= '</td></tr>';
        $footer .= parent::getCalendarFooter();
        return ($footer);
    }

    protected function getCalendarHeader(): string {
        global $ini;
        $base = $ini['server_dir'];
        $header = "<table class='minicalendar'>\n";
        
        $nextMonth = strtotime("next month", $this->getDate()) ;
        $previousMonth = strtotime("last month", $this->getDate()) ;
        $callback = $ini['server_dir'] . "/callbacks/getCalendar.php" ;

        $header .= "<caption class='calendar-month'>";
        $header .= "<div style='width:100%;'>" . $this->getMonthName() . " " . $this->getYear() . "</div>";
        $header .= "<div style='float:left' onclick='displayMonth($previousMonth, \"$callback\") ;'><img src='$base/images/arrowLeft.gif'></div>";
        $header .= "<div style='float:right;' onclick='displayMonth($nextMonth, \"$callback\") ;'><img src='$base/images/arrowRight.gif'></div>\n";
        $header .= "</caption>\n";

        $header .= "<tr class='calendar-header'><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>\n";
        return($header);
    }

}