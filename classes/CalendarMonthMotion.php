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
        $footer = '<tr><td colspan=7 align=center class=calendar-footer>';
        // Today button
        $footer .= '<input type=button value="' . gettext("today") . '" onclick="document.location=\'' . $_SERVER['PHP_SELF'] . '\'; " >';
        $footer .= '</td></tr>';
        $footer .= parent::getCalendarFooter();
        return ($footer);
    }

    protected function getCalendarHeader(): string {
        global $ini;
        $base = $ini['server_dir'];
        $header = "<table class='minicalendar'>\n";

        $header .= "\t<caption class='calendar-month'>";
        $header .= "<div><img src='$base/images/arrowLeft.gif'></div>";
        $header .= $this->getMonthName() . " " . $this->getYear();
        $header .= "<div><img src='$base/images/arrowRight.gif'></div>";
        $header .= "</caption>";

        $header .= "\t<tr class='calendar-header'><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>";
        return($header);
    }

}
