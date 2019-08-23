<?php

/**
 * Create and display a small HTML calendar in month view. For sidebars.
 * Defaults to display the current month.
 * @author nigel
 */
class CalendarMonthSmall {

    /** @var string The HTML representing the month calendar widget. */
    private $HTML = null;

    /** @var int The date to display, expressed as a unix timestamp. */
    private $theDate;

    /** @var int The number of blank days at the start of the month. */
    private $offset;

    /** @var int The number of days in the month. */
    private $daysInMonth;

    /**
     * 
     * @param int A Unix timestamp  (the number of seconds since January 1 1970 00:00:00 UTC).
     */
    function __construct($theDate = null) {
        $this->setDateFromTime($theDate);
    }

    public function setDateFromTime(int $theDate = null) {
        if (is_null($theDate)) {
            $theDate = time();
        }
        $this->theDate = $theDate;
        $this->setDaysInMonth();
        $this->setOffset();
    }

    public function setDateFromValues(int $year, int $month, int $day) {
        // NOTE: Using the format in this way could be a weak spot if used in another locale.
        $ts = mktime(0, 0, 0, $month, $day, $year);
        $this->setDateFromTime($ts);
    }

    protected function getCalendarHeader(): string {
        $header = "<table border=\"1\">\n";
        $header .= "\t<caption>". $this->getMonthName() . " " . $this->getYear() . "</caption>";
        $header .= "\t<tr><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>";
        return($header);
    }

    protected function getCalendarFooter(): string {
        $footer = "</table>\n";
        return($footer);
    }

    /**
     * Get the HTML code for the calendar.
     * @return string The html for the calendar.
     */
    public function getHTML(): string {
        $HTML = $this->getCalendarHeader();
        $HTML .= $this->getCalendarBody();
        $HTML .= $this->getCalendarFooter();
        return ($HTML);
    }

    private function getCalendarBody(): string {
        $HTML = "";

        for ($day = 0 - $this->offset + 1; $day <= $this->daysInMonth; $day += 7) {
            $HTML .= $this->getCalendarRow($day);
        }

        return($HTML);
    }

    /**
     * Override this function to allow links, scripts and formatting in cells.
     * @param string $date
     * @return string The content to place in the date cell. 
     */
    protected function getCalendarCellContent(string $date) {
        return $date;
    }

    /**
     * Get a row of dates representing a week on the calendar.
     * @param int $day The day whose row to get. Values less than 1 are converted into blanks to handle the first week starting on different days.
     * @return string An HTML string representing a row in the calendar (using tables).
     */
    private function getCalendarRow(int $day): string {
        $HTML = "<tr>";
        $dates = $this->getCalendarRowDates($day);
        for ($nCount = 0; $nCount < sizeof($dates); $nCount++) {
            $HTML .= "<td>" . $this->getCalendarCellContent($dates[$nCount]) . "</td>";
        }
        $HTML .= "</tr>\n";
        return ($HTML);
    }

    /**
     * Get an array of dates representing a week on a calendar.
     * @param int $day The starting day of the week. Passed 0 for the first week.
     * @return array The dates to include in a row of the calendar as an array of integers.
     */
    private function getCalendarRowDates(int $day): array {
        $dates = (array) [];

        for ($nCount = 0; $nCount < 7; $nCount++) {
            $date = $day + $nCount;
            if (($date < 1) || ($date > $this->daysInMonth)) {
                $szDate = "";
            } else {
                $szDate = "" . $date;
            }

            $dates[$nCount] = $szDate;
        }

        return ($dates);
    }

    /**
     * 
     * @return int The date as a Unix timestamp.
     */
    private function getDate(): int {
        return $this->theDate;
    }

    /**
     * 
     * @return int The number of days in the month.
     */
    private function numDaysInMonth(): int {
        return (date("t", $this->getDate()));
    }

    /**
     * 
     * @return string The full month name.
     */
    protected function getMonthName(): string {
        return (date("F", $this->getDate()));
    }

    /**
     * 
     * @return int The year as an integer.
     */
    protected function getYear(): int {
        return(date("Y", $this->getDate()));
    }

    /**
     * 
     * @return int The date of the day of the month.
     */
    private function getDay(): int {
        return (date("j", $this->getDate()));
    }

    /**
     * 
     * @return int The number of blank days to leave at the start of the month.
     */
    private function getStartingOffset(): int {
        return(date("w", $this->getDate()));
    }

    /*
     * Called when a datetime is set. 
     */

    private function setOffset() {
        $this->offset = $this->getStartingOffset();
    }

    /**
     * Called when a datetime is set.
     */
    private function setDaysInMonth() {
        $this->daysInMonth = $this->numDaysInMonth();
        }

    /** Use the code below for testing. */
        public static function test(){
        $cal = new CalendarMonthSmall();
        $year = date("Y", time()) ;

        for ($nCount = 0; $nCount < 12; $nCount++) {
            $cal->setDateFromValues($year, $nCount + 1, 1);
            echo $cal->getHTML();
        }
    }

}

//CalendarMonthSmall::test() ;